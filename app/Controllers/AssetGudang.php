<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AssetGudang extends BaseController
{
    // ─────────────────────────────────────────────────────────────
    //  INDEX – render halaman, kirim $gudang_list ke view
    // ─────────────────────────────────────────────────────────────
    public function index()
    {
        $db = \Config\Database::connect();

        $gudang_list = $db->table('gudang_utama')
            ->select('id, nama')
            ->orderBy('nama')
            ->get()->getResultArray();

        return view('inventory/asset_gudang', ['gudang_list' => $gudang_list]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DATA – endpoint AJAX
    //  GET /aset-gudang/data?gudang_id=3&q=semangka
    // ─────────────────────────────────────────────────────────────
    public function data(): ResponseInterface
    {
        $db       = \Config\Database::connect();
        $gudangId = (int) $this->request->getGet('gudang_id');
        $q        = trim($this->request->getGet('q') ?? '');

        if (!$gudangId) {
            return $this->response->setJSON(['data' => []]);
        }

        $builder = $db->table('stok_gudang sg')
            ->select([
                'sg.barang_id',
                'b.barcode',
                'b.nama     AS nama_barang',
                'sg.stock   AS total_qty',
                'sa.nama    AS satuan',
            ])
            ->join('barang b',  'b.id  = sg.barang_id',  'left')
            ->join('satuan sa', 'sa.id = sg.satuan_id',  'left')
            ->where('sg.gudang_id', $gudangId)
            ->orderBy('b.nama');

        if ($q !== '') {
            $builder->groupStart()
                ->like('b.nama',    $q)
                ->orLike('b.barcode', $q)
                ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$row) {
            $row['total_qty'] = (float) $row['total_qty'];
        }
        unset($row);

        return $this->response->setJSON(['data' => $rows]);
    }
}