<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LaporanPengirimanGudang extends BaseController
{
    // ─────────────────────────────────────────────────────────────
    //  INDEX – halaman utama laporan
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        return view('laporan/pengiriman_gudang', ['title' => 'Laporan Pengiriman Gudang']);
    }

    // ─────────────────────────────────────────────────────────────
    //  LIST – endpoint AJAX untuk Vue
    //  GET /laporan/pengiriman-gudang/list
    // ─────────────────────────────────────────────────────────────
    public function list(): ResponseInterface
    {
        $db = \Config\Database::connect();

        $tglAwal  = $this->request->getGet('tgl_awal')  ?? date('Y-m-01');
        $tglAkhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');
        $gudangId = $this->request->getGet('gudang_id');
        $cabangId = $this->request->getGet('cabang_id');

        // ── Main query ──────────────────────────────────────────
        $builder = $db->table('pengiriman_gudang pg')
            ->select([
                'pg.id',
                'pg.kode_pengiriman',
                'pg.gudang_id',
                'gu.nama      AS nama_gudang',
                'pg.cabang_id',
                'c.nama       AS nama_cabang',
                'pg.operator_id',
                'u.nama       AS nama_operator',
                'pg.waktu_pengiriman',
                'pg.status',
            ])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id', 'left')
            ->join('cabang c',        'c.id  = pg.cabang_id', 'left')
            ->join('users u',         'u.id  = pg.operator_id', 'left')
            ->where('DATE(pg.waktu_pengiriman) >=', $tglAwal)
            ->where('DATE(pg.waktu_pengiriman) <=', $tglAkhir)
            ->orderBy('pg.waktu_pengiriman', 'DESC');

        if ($gudangId) {
            $builder->where('pg.gudang_id', $gudangId);
        }
        if ($cabangId) {
            $builder->where('pg.cabang_id', $cabangId);
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
    //  DETAIL – halaman detail satu pengiriman
    //  GET /laporan/pengiriman-gudang/detail/{id}
    // ─────────────────────────────────────────────────────────────
    public function detail(int $id)
    {
        return view('laporan/pengiriman_gudang_detail', ['id' => $id, 'title' => 'Detail Pengiriman Gudang']);
    }

    // ─────────────────────────────────────────────────────────────
    //  GET-DETAIL – endpoint AJAX untuk Vue detail
    //  GET /laporan/pengiriman-gudang/get-detail/{id}
    // ─────────────────────────────────────────────────────────────
    public function getDetail(int $id): ResponseInterface
    {
        $db = \Config\Database::connect();

        // ── Header ──────────────────────────────────────────────
        $header = $db->table('pengiriman_gudang pg')
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
                'pg.created_at',
            ])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id', 'left')
            ->join('cabang c',        'c.id  = pg.cabang_id', 'left')
            ->join('users u',         'u.id  = pg.operator_id', 'left')
            ->where('pg.id', $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Not found']);
        }

        // ── Items ────────────────────────────────────────────────
        // Satuan diambil dari stok_gudang.satuan_id JOIN satuan.nama
        $items = $db->table('pengiriman_gudang_item pgi')
            ->select([
                'pgi.id',
                'pgi.barang_id',
                'b.nama       AS nama_barang',
                'pgi.qty',
                // satuan dari stok_gudang (matching gudang & barang)
                'sa.id        AS satuan_id',
                'sa.nama      AS nama_satuan',
            ])
            ->join('barang b',      'b.id  = pgi.barang_id', 'left')
            // join stok_gudang untuk mendapatkan satuan yang dipakai di gudang tersebut
            ->join(
                'stok_gudang sg',
                'sg.barang_id = pgi.barang_id AND sg.gudang_id = ' . (int)$header['gudang_id'],
                'left'
            )
            ->join('satuan sa',     'sa.id = sg.satuan_id', 'left')
            ->where('pgi.pengiriman_gudang_id', $id)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'header' => $header,
            'items'  => $items,
        ]);
    }
}