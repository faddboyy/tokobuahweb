<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

class PengirimanGudang extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function resolveGudangId(int $userId): ?int
    {
        $gudang = $this->db->table('gudang_utama')->where('mandor_id', $userId)->get()->getRow();
        if ($gudang) return (int) $gudang->id;

        $gudang = $this->db->table('gudang_utama')->orderBy('id', 'ASC')->limit(1)->get()->getRow();
        return $gudang ? (int) $gudang->id : null;
    }

    // ── GET /pengiriman-gudang ───────────────────────────────────────────────
    public function index()
    {
        return view('transaksi/pengiriman_gudang', ['title' => 'Pengiriman Gudang ke Toko']);
    }

    // ── GET /pengiriman-gudang/search-barang?q=xxx ───────────────────────────
    public function searchBarang()
    {
        $operator_id = (int) session()->get('user_id');
        $gudang_id   = $this->resolveGudangId($operator_id);

        if (!$gudang_id) {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Gudang tidak ditemukan untuk operator ini']);
        }

        $q = trim($this->request->getGet('q') ?? '');

        $builder = $this->db->table('stok_gudang sg')
            ->select([
                'sg.barang_id',
                'sg.stock',
                'b.nama    AS nama_barang',
                'b.barcode',
                's.id      AS satuan_id',
                's.nama    AS nama_satuan',
            ])
            ->join('barang b', 'b.id = sg.barang_id', 'left')
            ->join('satuan s', 's.id = sg.satuan_id',  'left')
            ->where('sg.gudang_id', $gudang_id)
            ->where('sg.stock >', 0);

        if ($q !== '') {
            $builder->groupStart()
                ->like('b.nama', $q)
                ->orLike('b.barcode', $q)
                ->groupEnd();
        }

        $results = $builder->orderBy('b.nama', 'ASC')->limit(15)->get()->getResultArray();

        foreach ($results as &$r) {
            $r['stock'] = (float) $r['stock'];
        }
        unset($r);

        return $this->response->setJSON([
            'status'    => true,
            'gudang_id' => $gudang_id,
            'results'   => $results,
        ]);
    }

    // ── GET /pengiriman-gudang/get-cabang ────────────────────────────────────
    public function getCabang()
    {
        $cabang = $this->db->table('cabang')
            ->select('id, nama')
            ->orderBy('nama', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON(['status' => true, 'cabang' => $cabang]);
    }

    // ── POST /pengiriman-gudang/simpan ───────────────────────────────────────
    public function simpan()
    {
        $operator_id = (int) session()->get('user_id');
        if (!$operator_id) {
            return $this->response->setStatusCode(401)
                ->setJSON(['message' => 'Session tidak valid, silakan login ulang']);
        }

        $gudang_id = $this->resolveGudangId($operator_id);
        if (!$gudang_id) {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Gudang tidak ditemukan untuk operator ini']);
        }

        $data      = $this->request->getJSON(true);
        $cabang_id = (int) ($data['cabang_id'] ?? 0);
        $items     = $data['items'] ?? [];

        if (!$cabang_id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Toko tujuan wajib dipilih']);
        }

        $cabang = $this->db->table('cabang')->where('id', $cabang_id)->get()->getRow();
        if (!$cabang) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Toko tujuan tidak ditemukan']);
        }

        if (empty($items)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Tidak ada item pengiriman']);
        }

        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 0);
            if ($qty <= 0) {
                return $this->response->setStatusCode(400)
                    ->setJSON(['message' => 'Qty setiap barang harus lebih dari 0']);
            }

            $stok = $this->db->table('stok_gudang')
                ->where('gudang_id', $gudang_id)
                ->where('barang_id', (int) $item['barang_id'])
                ->get()->getRow();

            if (!$stok || (float) $stok->stock < $qty) {
                return $this->response->setStatusCode(422)
                    ->setJSON([
                        'message' => 'Stok "' . ($item['nama_barang'] ?? '-') . '" tidak mencukupi'
                            . ' (tersedia: ' . number_format((float) ($stok->stock ?? 0), 2) . ')',
                    ]);
            }
        }

        $this->db->transBegin();
        try {
            $kode_pengiriman  = 'KRG-' . date('YmdHis');
            $waktu_pengiriman = date('Y-m-d H:i:s');

            $this->db->table('pengiriman_gudang')->insert([
                'kode_pengiriman'  => $kode_pengiriman,
                'gudang_id'        => $gudang_id,
                'cabang_id'        => $cabang_id,
                'operator_id'      => $operator_id,
                'waktu_pengiriman' => $waktu_pengiriman,
                'status'           => 'dikirim',
                'created_at'       => $waktu_pengiriman,
            ]);

            $pengiriman_id = $this->db->insertID();

            foreach ($items as $item) {
                $qty       = (float) $item['qty'];
                $barang_id = (int)   $item['barang_id'];
                $satuan_id = (int)   $item['satuan_id'];

                $this->db->table('pengiriman_gudang_item')->insert([
                    'pengiriman_gudang_id' => $pengiriman_id,
                    'barang_id'            => $barang_id,
                    'qty'                  => $qty,
                ]);

                $stok = $this->db->table('stok_gudang')
                    ->where('gudang_id', $gudang_id)
                    ->where('barang_id', $barang_id)
                    ->get()->getRow();

                $this->db->table('stok_gudang')
                    ->where('id', $stok->id)
                    ->update(['stock' => (float) $stok->stock - $qty]);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'status'          => true,
                'kode_pengiriman' => $kode_pengiriman,
                'pengiriman_id'   => $pengiriman_id,
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    // ── GET /pengiriman-gudang/cetak/:id ─────────────────────────────────────
    // Generate PDF surat pengiriman menggunakan dompdf.
    // Barcode di-generate dari kode_pengiriman menggunakan library picqer/php-barcode-generator
    // yang biasanya sudah ter-include bersama dompdf via composer.
    // Jika belum: composer require picqer/php-barcode-generator
    public function cetakSurat($id)
    {
        // ── Ambil data header ────────────────────────────────────────────
        $pengiriman = $this->db->table('pengiriman_gudang pg')
            ->select([
                'pg.*',
                'gu.nama  AS nama_gudang',
                'c.nama   AS nama_cabang',
                'u.nama   AS nama_operator',
            ])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id',   'left')
            ->join('cabang c',        'c.id  = pg.cabang_id',   'left')
            ->join('users u',         'u.id  = pg.operator_id', 'left')
            ->where('pg.id', $id)
            ->get()->getRow();

        if (!$pengiriman) {
            return $this->response->setStatusCode(404)
                ->setBody('<h3 style="font-family:sans-serif;color:red">Data pengiriman tidak ditemukan</h3>');
        }

        // ── Ambil items ──────────────────────────────────────────────────
        $items = $this->db->table('pengiriman_gudang_item pgi')
            ->select([
                'pgi.qty',
                'b.nama AS nama_barang',
                'b.barcode',
                'sg.satuan_id',
                's.nama AS nama_satuan',
            ])
            ->join('barang b', 'b.id = pgi.barang_id', 'left')
            ->join('stok_gudang sg', 'sg.barang_id = pgi.barang_id', 'left')
            ->join('satuan s', 's.id = sg.satuan_id',  'left')
            ->where('pgi.pengiriman_gudang_id', $id)
            ->get()->getResult();

        // ── Generate barcode (Code128) sebagai base64 PNG ────────────────
        $barcode_base64 = $this->generateBarcodeBase64($pengiriman->kode_pengiriman);

        // ── Render HTML template ─────────────────────────────────────────
        $html = view('transaksi/surat_pengiriman_pdf', [
            'pengiriman'      => $pengiriman,
            'items'           => $items,
            'barcode_base64'  => $barcode_base64,
        ]);

        // ── Dompdf ──────────────────────────────────────────────────────
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'surat_pengiriman_' . $pengiriman->kode_pengiriman . '.pdf';

        // Stream PDF ke browser (inline = buka langsung, attachment = download)
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }

    // ── GET /pengiriman-gudang/cetak-download/:id ────────────────────────────
    // Versi download (force download)
    public function cetakDownload($id)
    {
        // Reuse cetakSurat logic dengan Attachment => true
        // Cukup panggil cetakSurat dan override stream parameter
        $pengiriman = $this->db->table('pengiriman_gudang pg')
            ->select(['pg.*', 'gu.nama AS nama_gudang', 'c.nama AS nama_cabang', 'u.nama AS nama_operator'])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id', 'left')
            ->join('cabang c',        'c.id  = pg.cabang_id', 'left')
            ->join('users u',         'u.id  = pg.operator_id', 'left')
            ->where('pg.id', $id)->get()->getRow();

        if (!$pengiriman) {
            return $this->response->setStatusCode(404)->setBody('Data tidak ditemukan');
        }

        $items = $this->db->table('pengiriman_gudang_item pgi')
            ->select(['pgi.qty', 'b.nama AS nama_barang', 'b.barcode', 's.nama AS nama_satuan'])
            ->join('barang b', 'b.id = pgi.barang_id', 'left')
            ->join('satuan s', 's.id = pgi.satuan_id',  'left')
            ->where('pgi.pengiriman_gudang_id', $id)
            ->get()->getResult();

        $barcode_base64 = $this->generateBarcodeBase64($pengiriman->kode_pengiriman);

        $html = view('transaksi/surat_pengiriman_pdf', [
            'pengiriman'     => $pengiriman,
            'items'          => $items,
            'barcode_base64' => $barcode_base64,
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('surat_pengiriman_' . $pengiriman->kode_pengiriman . '.pdf', ['Attachment' => true]);
        exit;
    }

    // ── POST /pengiriman-gudang/batalkan/:id ─────────────────────────────────
    public function batalkan($pengiriman_id)
    {
        $pengiriman = $this->db->table('pengiriman_gudang')
            ->where('id', $pengiriman_id)->get()->getRow();

        if (!$pengiriman) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Data pengiriman tidak ditemukan']);
        }
        if ($pengiriman->status === 'dibatalkan') {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Pengiriman ini sudah dibatalkan sebelumnya']);
        }
        if ($pengiriman->status === 'diterima') {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Pengiriman yang sudah diterima tidak dapat dibatalkan']);
        }

        $items = $this->db->table('pengiriman_gudang_item')
            ->where('pengiriman_gudang_id', $pengiriman_id)
            ->get()->getResultArray();

        $this->db->transBegin();
        try {
            foreach ($items as $item) {
                $stok = $this->db->table('stok_gudang')
                    ->where('gudang_id', $pengiriman->gudang_id)
                    ->where('barang_id', $item['barang_id'])
                    ->get()->getRow();

                if ($stok) {
                    $this->db->table('stok_gudang')->where('id', $stok->id)
                        ->update(['stock' => (float) $stok->stock + (float) $item['qty']]);
                } else {
                    $this->db->table('stok_gudang')->insert([
                        'gudang_id' => $pengiriman->gudang_id,
                        'barang_id' => $item['barang_id'],
                        'satuan_id' => $item['satuan_id'],
                        'stock'     => (float) $item['qty'],
                    ]);
                }
            }

            $this->db->table('pengiriman_gudang')->where('id', $pengiriman_id)
                ->update(['status' => 'dibatalkan']);

            $this->db->transCommit();
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Pengiriman berhasil dibatalkan dan stok telah dikembalikan.',
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    // ── Helper: generate barcode Code128 → base64 PNG ────────────────────────
    // Membutuhkan: composer require picqer/php-barcode-generator
    private function generateBarcodeBase64(string $code): string
    {
        try {
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $png       = $generator->getBarcode($code, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128);
            return base64_encode($png);
        } catch (\Throwable $e) {
            // Fallback: kembalikan 1x1 pixel PNG transparan jika library tidak tersedia
            return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        }
    }
}
