<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class BarangMasuk extends BaseController
{
    // ─────────────────────────────────────────────────────────────
    //  Halaman utama input barang masuk
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        return view('transaksi/barang_masuk');
    }

    // ─────────────────────────────────────────────────────────────
    //  AJAX: scan / cari kode pengiriman
    //  GET /barang-masuk/scan?kode=KRG-xxx
    //  Hanya ambil pengiriman yang:
    //    - status = 'dikirim'
    //    - cabang_id = session cabang_id (petugas toko)
    // ─────────────────────────────────────────────────────────────
    public function scan(): ResponseInterface
    {
        $db       = \Config\Database::connect();
        $kode     = trim($this->request->getGet('kode') ?? '');
        $cabangId = session()->get('cabang_id');

        if (!$kode) {
            return $this->response->setJSON(['success' => false, 'message' => 'Kode pengiriman kosong.']);
        }

        // ── cari header pengiriman ──────────────────────────────
        $pengiriman = $db->table('pengiriman_gudang pg')
            ->select([
                'pg.id',
                'pg.kode_pengiriman',
                'pg.gudang_id',
                'gu.nama  AS nama_gudang',
                'pg.cabang_id',
                'c.nama   AS nama_cabang',
                'pg.operator_id',
                'u.nama   AS nama_operator',
                'pg.waktu_pengiriman',
                'pg.status',
            ])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id', 'left')
            ->join('cabang c',        'c.id  = pg.cabang_id', 'left')
            ->join('users u',         'u.id  = pg.operator_id', 'left')
            ->where('pg.kode_pengiriman', $kode)
            ->where('pg.status', 'dikirim')
            ->where('pg.cabang_id', (int) $cabangId)
            ->get()->getRowArray();

        if (!$pengiriman) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kode pengiriman tidak ditemukan, bukan milik cabang Anda, atau sudah diproses.',
            ]);
        }

        // ── cek apakah sudah ada barang_masuk untuk pengiriman ini ──
        $sudahAda = $db->table('barang_masuk')
            ->where('pengiriman_gudang_id', $pengiriman['id'])
            ->countAllResults();

        if ($sudahAda) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengiriman ini sudah pernah diproses sebagai barang masuk.',
            ]);
        }

        // ── ambil item pengiriman + satuan kirim & satuan simpan ──
        // satuan_kirim  : dari stok_gudang.satuan_id (satuan gudang pengirim)
        // satuan_simpan : dari barang.satuan_id      (satuan default barang)
        $items = $db->table('pengiriman_gudang_item pgi')
            ->select([
                'pgi.id',
                'pgi.barang_id',
                'b.nama          AS nama_barang',
                'b.barcode',
                // qty kiriman dari gudang
                'pgi.qty         AS qty_kiriman',
                // satuan kirim: dari stok_gudang pada gudang pengirim
                'sk.id           AS satuan_kirim_id',
                'sk.nama         AS satuan_kirim',
                // satuan simpan: dari barang.satuan_id
                'ss.id           AS satuan_simpan_id',
                'ss.nama         AS satuan_simpan',
            ])
            ->join(
                'barang b',
                'b.id = pgi.barang_id',
                'left'
            )
            ->join(
                'stok_gudang sg',
                'sg.barang_id = pgi.barang_id AND sg.gudang_id = ' . (int)$pengiriman['gudang_id'],
                'left'
            )
            ->join('satuan sk', 'sk.id = sg.satuan_id', 'left')
            ->join('satuan ss', 'ss.id = b.satuan_id',  'left')
            ->where('pgi.pengiriman_gudang_id', $pengiriman['id'])
            ->get()->getResultArray();

        return $this->response->setJSON([
            'success'    => true,
            'pengiriman' => $pengiriman,
            'items'      => $items,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  AJAX: simpan barang masuk
    //  POST /barang-masuk/simpan
    // ─────────────────────────────────────────────────────────────
    public function simpan(): ResponseInterface
    {
        $db         = \Config\Database::connect();
        $session    = session();
        $operatorId = $session->get('user_id');
        $cabangId   = $session->get('cabang_id');

        $input            = $this->request->getJSON(true);
        $pengirimanId     = (int) ($input['pengiriman_gudang_id'] ?? 0);
        $items            = $input['items'] ?? [];

        if (!$pengirimanId || empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap.']);
        }

        // ── validasi ulang pengiriman ──────────────────────────
        $pengiriman = $db->table('pengiriman_gudang')
            ->where('id', $pengirimanId)
            ->where('status', 'dikirim')
            ->where('cabang_id', (int) $cabangId)
            ->get()->getRowArray();

        if (!$pengiriman) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengiriman tidak valid.']);
        }

        // ── generate kode masuk ───────────────────────────────
        $kodeMasuk = 'BM-' . date('YmdHis');

        $db->transStart();

        // ── insert barang_masuk ───────────────────────────────
        $db->table('barang_masuk')->insert([
            'kode_masuk'           => $kodeMasuk,
            'pengiriman_gudang_id' => $pengirimanId,
            'waktu_masuk'          => date('Y-m-d H:i:s'),
            'operator_id'          => $operatorId,
            'cabang_id'            => $cabangId,
        ]);
        $barangMasukId = $db->insertID();

        // ── insert barang_masuk_item ───────────────────────────
        foreach ($items as $item) {
            $qtyKiriman  = (float) ($item['qty_kiriman']  ?? 0);
            $qtyAktual   = (float) ($item['qty_aktual']   ?? 0);
            $selisih     = (float) ($item['selisih']      ?? 0);
            $satKirim    = $item['satuan_kirim']  ?? '';
            $satSimpan   = $item['satuan_simpan'] ?? '';

            $db->table('barang_masuk_item')->insert([
                'barang_masuk_id' => $barangMasukId,
                'barang_id'       => (int) $item['barang_id'],
                'qty_kiriman'     => $qtyKiriman,
                'qty_aktual'      => $qtyAktual,
                'selisih'         => $selisih,
                'satuan_kirim'    => $satKirim,
                'satuan_simpan'   => $satSimpan,
            ]);

            // ── update stok inventory cabang ──────────────────
            $existing = $db->table('inventory')
                ->where('barang_id', (int) $item['barang_id'])
                ->where('cabang_id', (int) $cabangId)
                ->get()->getRowArray();

            if ($existing) {
                $db->table('inventory')
                    ->where('barang_id', (int) $item['barang_id'])
                    ->where('cabang_id', (int) $cabangId)
                    ->update(['stock' => $existing['stock'] + $qtyAktual]);
            } else {
                $db->table('inventory')->insert([
                    'barang_id' => (int) $item['barang_id'],
                    'cabang_id' => (int) $cabangId,
                    'stock'     => $qtyAktual,
                ]);
            }
        }

        // ── update status pengiriman → diterima ───────────────
        $db->table('pengiriman_gudang')
            ->where('id', $pengirimanId)
            ->update(['status' => 'diterima']);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan, silakan coba lagi.']);
        }

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Barang masuk berhasil disimpan.',
            'kode_masuk' => $kodeMasuk,
        ]);
    }
}
