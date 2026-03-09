<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LaporanBarangMasuk extends BaseController
{
    // ─────────────────────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        return view('laporan/barang_masuk', ['title' => 'Laporan Barang Masuk']);
    }

    // ─────────────────────────────────────────────────────────────
    //  LIST  GET /laporan/barang-masuk/list
    // ─────────────────────────────────────────────────────────────
    public function list(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $tglAwal  = $this->request->getGet('tgl_awal')  ?? date('Y-m-01');
        $tglAkhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');
        $gudangId = $this->request->getGet('gudang_id');
        $cabangId = $this->request->getGet('cabang_id');

        $builder = $db->table('barang_masuk bm')
            ->select([
                'bm.id',
                'bm.kode_masuk',
                'bm.status',
                'bm.reason',
                'bm.voided_by',
                'voider.nama        AS nama_voided_by',
                'bm.voided_at',
                'bm.pengiriman_gudang_id',
                'pg.kode_pengiriman',
                'pg.gudang_id',
                'gu.nama            AS nama_gudang',
                'pg.operator_id     AS operator_kirim_id',
                'op_kirim.nama      AS nama_operator_kirim',
                'bm.cabang_id',
                'c.nama             AS nama_cabang',
                'bm.operator_id     AS operator_masuk_id',
                'op_masuk.nama      AS nama_operator_masuk',
                'bm.waktu_masuk',
            ])
            ->join('pengiriman_gudang pg',  'pg.id  = bm.pengiriman_gudang_id', 'left')
            ->join('gudang_utama gu',        'gu.id  = pg.gudang_id',            'left')
            ->join('users op_kirim',         'op_kirim.id = pg.operator_id',     'left')
            ->join('cabang c',               'c.id   = bm.cabang_id',            'left')
            ->join('users op_masuk',         'op_masuk.id = bm.operator_id',     'left')
            ->join('users voider',           'voider.id = bm.voided_by',         'left')
            ->where('DATE(bm.waktu_masuk) >=', $tglAwal)
            ->where('DATE(bm.waktu_masuk) <=', $tglAkhir)
            ->orderBy('bm.waktu_masuk', 'DESC');

        if ($gudangId) $builder->where('pg.gudang_id', $gudangId);
        if ($cabangId) $builder->where('bm.cabang_id', $cabangId);

        $rows       = $builder->get()->getResultArray();
        $gudangList = $db->table('gudang_utama')->select('id, nama')->get()->getResultArray();
        $cabangList = $db->table('cabang')->select('id, nama')->orderBy('nama')->get()->getResultArray();

        return $this->response->setJSON([
            'data'        => $rows,
            'gudang_list' => $gudangList,
            'cabang_list' => $cabangList,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DETAIL page  GET /laporan/barang-masuk/detail/{id}
    // ─────────────────────────────────────────────────────────────
    public function detail(int $id)
    {
        return view('laporan/barang_masuk_detail', ['id' => $id, 'title' => 'Detail Barang Masuk']);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET-DETAIL  GET /laporan/barang-masuk/get-detail/{id}
    // ─────────────────────────────────────────────────────────────
    public function getDetail(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();

        $header = $db->table('barang_masuk bm')
            ->select([
                'bm.id',
                'bm.kode_masuk',
                'bm.status',
                'bm.reason',
                'bm.voided_by',
                'voider.nama        AS nama_voided_by',
                'bm.voided_at',
                'bm.pengiriman_gudang_id',
                'pg.kode_pengiriman',
                'pg.gudang_id',
                'gu.nama            AS nama_gudang',
                'pg.operator_id     AS operator_kirim_id',
                'op_kirim.nama      AS nama_operator_kirim',
                'bm.cabang_id',
                'c.nama             AS nama_cabang',
                'bm.operator_id     AS operator_masuk_id',
                'op_masuk.nama      AS nama_operator_masuk',
                'bm.waktu_masuk',
            ])
            ->join('pengiriman_gudang pg',  'pg.id  = bm.pengiriman_gudang_id', 'left')
            ->join('gudang_utama gu',        'gu.id  = pg.gudang_id',            'left')
            ->join('users op_kirim',         'op_kirim.id = pg.operator_id',     'left')
            ->join('cabang c',               'c.id   = bm.cabang_id',            'left')
            ->join('users op_masuk',         'op_masuk.id = bm.operator_id',     'left')
            ->join('users voider',           'voider.id = bm.voided_by',         'left')
            ->where('bm.id', $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Not found']);
        }

        $items = $db->table('barang_masuk_item bmi')
            ->select([
                'bmi.id',
                'bmi.barang_id',
                'b.nama          AS nama_barang',
                'b.barcode',
                'bmi.qty_kiriman',
                'bmi.qty_aktual',
                'bmi.selisih',
                'bmi.satuan_kirim',
                'bmi.satuan_simpan',
            ])
            ->join('barang b', 'b.id = bmi.barang_id', 'left')
            ->where('bmi.barang_masuk_id', $id)
            ->get()->getResultArray();

        foreach ($items as &$item) {
            $item['qty_kiriman'] = (float) $item['qty_kiriman'];
            $item['qty_aktual']  = (float) $item['qty_aktual'];
            $item['selisih']     = (float) $item['selisih'];
        }
        unset($item);

        return $this->response->setJSON([
            'header' => $header,
            'items'  => $items,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  VOID  POST /laporan/barang-masuk/void/{id}
    //  Hanya owner / admin.
    // ─────────────────────────────────────────────────────────────
    public function void(int $id): ResponseInterface
    {
        $role        = session()->get('role');
        $operator_id = (int) session()->get('user_id');

        if (!in_array($role, ['owner', 'admin'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['message' => 'Akses ditolak. Hanya owner / admin yang dapat melakukan void.']);
        }

        $db = \Config\Database::connect();

        $bm = $db->table('barang_masuk')->where('id', $id)->get()->getRow();
        if (!$bm) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Data barang masuk tidak ditemukan']);
        }
        if (($bm->status ?? 'selesai') === 'dibatalkan') {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Barang masuk ini sudah dibatalkan sebelumnya']);
        }

        $body   = $this->request->getJSON(true);
        $reason = trim($body['reason'] ?? '');
        if ($reason === '') {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Alasan void (reason) wajib diisi']);
        }

        $items = $db->table('barang_masuk_item')
            ->where('barang_masuk_id', $id)
            ->get()->getResultArray();

        if (empty($items)) {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Tidak ada item pada barang masuk ini']);
        }

        $db->transBegin();
        try {
            foreach ($items as $item) {
                $barang_id  = (int)   $item['barang_id'];
                $qty_aktual = (float) $item['qty_aktual'];
                $cabang_id  = (int)   $bm->cabang_id;

                $inv = $db->table('inventory')
                    ->where('barang_id', $barang_id)
                    ->where('cabang_id', $cabang_id)
                    ->get()->getRow();

                if ($inv) {
                    $db->table('inventory')
                        ->where('id', $inv->id)
                        ->update(['stock' => max(0, $inv->stock - $qty_aktual)]);
                }
            }

            $db->table('barang_masuk')->where('id', $id)->update([
                'status'    => 'dibatalkan',
                'reason'    => $reason,
                'voided_by' => $operator_id,
                'voided_at' => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();

            // Ambil nama voider untuk dikembalikan ke frontend
            $voider = $db->table('users')->select('nama')->where('id', $operator_id)->get()->getRow();

            return $this->response->setJSON([
                'status'         => true,
                'message'        => 'Barang masuk berhasil di-void. Stok inventory telah dikembalikan.',
                'nama_voided_by' => $voider?->nama ?? '',
                'voided_at'      => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
