<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LaporanPenerimaanGudang extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─────────────────────────────────────────────
    // GET /laporan/penerimaan-gudang
    // ─────────────────────────────────────────────
    public function index()
    {
        return view('laporan/penerimaan_gudang', ['title' => 'Laporan Penerimaan Gudang']);
    }

    // ─────────────────────────────────────────────
    // GET /laporan/penerimaan-gudang/list
    // ─────────────────────────────────────────────
    public function list()
    {
        $tglAwal         = $this->request->getGet('tgl_awal')    ?? date('Y-m-01');
        $tglAkhir        = $this->request->getGet('tgl_akhir')   ?? date('Y-m-d');
        $gudangId        = $this->request->getGet('gudang_id');
        $suplierIdFilter = $this->request->getGet('suplier_id');

        $builder = $this->db->table('penerimaan_gudang pg')
            ->select([
                'pg.id',
                'pg.kode_penerimaan',
                'pg.kode_supplier',
                'pg.status',
                'pg.waktu_penerimaan',
                'pg.gudang_id',
                'gu.nama AS nama_gudang',
                'sj.kode_po',
                'sj.suplier_id',
                'sp.nama AS nama_suplier',
                'pg.operator_id',
                'u.nama AS nama_operator',
            ])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id',      'left')
            ->join('surat_jalan sj',  'sj.id = pg.surat_jalan_id', 'left')
            ->join('suplier sp',      'sp.id = sj.suplier_id',     'left')
            ->join('users u',         'u.id  = pg.operator_id',    'left')
            ->where('DATE(pg.waktu_penerimaan) >=', $tglAwal)
            ->where('DATE(pg.waktu_penerimaan) <=', $tglAkhir)
            ->orderBy('pg.waktu_penerimaan', 'DESC');

        if (!empty($gudangId)) {
            $builder->where('pg.gudang_id', $gudangId);
        }
        if (!empty($suplierIdFilter)) {
            $builder->where('sj.suplier_id', $suplierIdFilter);
        }

        $rows = $builder->get()->getResultArray();

        $gudangList = $this->db->table('gudang_utama')
            ->select('id, nama')->orderBy('nama')->get()->getResultArray();

        $suplierList = $this->db->table('suplier')
            ->select('id, nama')->orderBy('nama')->get()->getResultArray();

        return $this->response->setJSON([
            'data'         => $rows,
            'gudang_list'  => $gudangList,
            'suplier_list' => $suplierList,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /laporan/penerimaan-gudang/detail/{id}
    // ─────────────────────────────────────────────
    public function detail($id = null)
    {
        if (empty($id)) {
            return redirect()->to(base_url('laporan/penerimaan-gudang'));
        }

        return view('laporan/penerimaan_gudang_detail', ['id' => (int) $id, 'title' => 'Detail Penerimaan Gudang']);
    }

    // ─────────────────────────────────────────────
    // GET /laporan/penerimaan-gudang/get-detail/{id}
    // ─────────────────────────────────────────────
    public function getDetail($id = null)
    {
        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'ID tidak valid']);
        }

        // ── Header ────────────────────────────────
        $header = $this->db->table('penerimaan_gudang pg')
            ->select([
                'pg.id',
                'pg.kode_penerimaan',
                'pg.kode_supplier',
                'pg.status',
                'pg.waktu_penerimaan',
                'gu.nama AS nama_gudang',
                'sj.id AS surat_jalan_id',
                'sj.kode_po',
                'sj.suplier_id',
                'sp.nama AS nama_suplier',
                'u.nama AS nama_operator',
            ])
            ->join('gudang_utama gu', 'gu.id = pg.gudang_id',      'left')
            ->join('surat_jalan sj',  'sj.id = pg.surat_jalan_id', 'left')
            ->join('suplier sp',      'sp.id = sj.suplier_id',     'left')
            ->join('users u',         'u.id  = pg.operator_id',    'left')
            ->where('pg.id', $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        // ── Items ─────────────────────────────────
        // Satuan diambil dari surat_jalan_item (PO) — 1 satuan per barang per PO
        $sjId = (int) $header['surat_jalan_id'];

        $items = $this->db->table('penerimaan_gudang_item pgi')
            ->select([
                'pgi.id',
                'pgi.barang_id',
                'b.nama AS nama_barang',
                'pgi.qty_dipesan',
                'pgi.qty_diterima',
                '(pgi.qty_diterima - pgi.qty_dipesan) AS selisih',
                'sat.nama AS nama_satuan',
            ])
            ->join('barang b',             'b.id = pgi.barang_id',                                         'left')
            ->join('surat_jalan_item sji',  "sji.barang_id = pgi.barang_id AND sji.surat_jalan_id = $sjId", 'left')
            ->join('satuan sat',            'sat.id = sji.satuan_id',                                       'left')
            ->where('pgi.penerimaan_gudang_id', $id)
            ->orderBy('b.nama', 'ASC')
            ->get()->getResultArray();

        foreach ($items as &$item) {
            $item['qty_dipesan']  = (int) $item['qty_dipesan'];
            $item['qty_diterima'] = (int) $item['qty_diterima'];
            $item['selisih']      = (int) $item['selisih'];
        }
        unset($item);

        return $this->response->setJSON([
            'header' => $header,
            'items'  => $items,
        ]);
    }
}
