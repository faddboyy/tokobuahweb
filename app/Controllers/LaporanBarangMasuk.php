<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LaporanBarangMasuk extends BaseController
{
    // ─────────────────────────────────────────────────────────────
    //  INDEX – halaman utama laporan
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        return view('laporan/barang_masuk', ['title' => 'Laporan Barang Masuk']);
    }

    // ─────────────────────────────────────────────────────────────
    //  LIST – endpoint AJAX untuk Vue
    //  GET /laporan/barang-masuk/list
    // ─────────────────────────────────────────────────────────────
    public function list(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $tglAwal  = $this->request->getGet('tgl_awal')  ?? date('Y-m-01');
        $tglAkhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');
        $gudangId = $this->request->getGet('gudang_id');
        $cabangId = $this->request->getGet('cabang_id');

        // ── Main query ──────────────────────────────────────────
        // barang_masuk (bm)
        //   → pengiriman_gudang (pg) untuk info gudang pengirim & operator pengirim
        //   → gudang_utama (gu)
        //   → users op_kirim  : operator pengiriman (gudang)
        //   → users op_masuk  : operator barang masuk (toko)
        //   → cabang (c)
        $builder = $db->table('barang_masuk bm')
            ->select([
                'bm.id',
                'bm.kode_masuk',
                'bm.pengiriman_gudang_id',
                'pg.kode_pengiriman',
                // Gudang pengirim
                'pg.gudang_id',
                'gu.nama            AS nama_gudang',
                // Operator pengiriman (dari gudang)
                'pg.operator_id     AS operator_kirim_id',
                'op_kirim.nama      AS nama_operator_kirim',
                // Cabang penerima
                'bm.cabang_id',
                'c.nama             AS nama_cabang',
                // Operator barang masuk (petugas toko)
                'bm.operator_id     AS operator_masuk_id',
                'op_masuk.nama      AS nama_operator_masuk',
                'bm.waktu_masuk',
            ])
            ->join('pengiriman_gudang pg',  'pg.id  = bm.pengiriman_gudang_id', 'left')
            ->join('gudang_utama gu',        'gu.id  = pg.gudang_id',            'left')
            ->join('users op_kirim',         'op_kirim.id = pg.operator_id',     'left')
            ->join('cabang c',               'c.id   = bm.cabang_id',            'left')
            ->join('users op_masuk',         'op_masuk.id = bm.operator_id',     'left')
            ->where('DATE(bm.waktu_masuk) >=', $tglAwal)
            ->where('DATE(bm.waktu_masuk) <=', $tglAkhir)
            ->orderBy('bm.waktu_masuk', 'DESC');

        if ($gudangId) {
            $builder->where('pg.gudang_id', $gudangId);
        }
        if ($cabangId) {
            $builder->where('bm.cabang_id', $cabangId);
        }

        $rows = $builder->get()->getResultArray();

        // ── Dropdown lists ──────────────────────────────────────
        $gudangList = $db->table('gudang_utama')->select('id, nama')->get()->getResultArray();
        $cabangList = $db->table('cabang')->select('id, nama')->orderBy('nama')->get()->getResultArray();

        return $this->response->setJSON([
            'data'        => $rows,
            'gudang_list' => $gudangList,
            'cabang_list' => $cabangList,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DETAIL – halaman detail satu barang masuk
    //  GET /laporan/barang-masuk/detail/{id}
    // ─────────────────────────────────────────────────────────────
    public function detail(int $id)
    {
        return view('laporan/barang_masuk_detail', ['id' => $id, 'title' => 'Detail Barang Masuk']);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET-DETAIL – endpoint AJAX untuk Vue detail
    //  GET /laporan/barang-masuk/get-detail/{id}
    // ─────────────────────────────────────────────────────────────
    public function getDetail(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();

        // ── Header ──────────────────────────────────────────────
        $header = $db->table('barang_masuk bm')
            ->select([
                'bm.id',
                'bm.kode_masuk',
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
            ->where('bm.id', $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Not found']);
        }

        // ── Items ────────────────────────────────────────────────
        // qty_kiriman  = kiriman dari gudang (satuan_kirim)
        // qty_aktual   = aktual diterima toko (satuan_simpan)
        // selisih      = manual dari petugas
        // satuan_kirim / satuan_simpan = kolom teks di barang_masuk_item
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

        // cast numerik
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
}
