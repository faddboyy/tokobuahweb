<?php

namespace App\Controllers;

use App\Models\SatuanModel;
use CodeIgniter\RESTful\ResourceController;

class Satuan extends ResourceController
{
    protected $modelName = 'App\Models\SatuanModel';
    protected $format    = 'json';

    /**
     * Mengambil semua data satuan untuk dropdown/list inline
     */
    public function list()
    {
        $data = $this->model->orderBy('nama', 'ASC')->findAll();
        return $this->respond([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * Menyimpan satuan baru (Inline dari Modal Barang)
     */
    public function store()
    {
        $nama = $this->request->getVar('nama');

        if (empty($nama)) {
            return $this->fail('Nama satuan tidak boleh kosong');
        }

        $id = $this->model->insert([
            'nama' => $nama
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Satuan baru berhasil ditambahkan',
            'id'      => $id
        ]);
    }

    /**
     * Menghapus satuan (Inline dari Modal Barang)
     */
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Satuan tidak ditemukan');
        }

        try {
            $this->model->delete($id);
            return $this->respond([
                'status'  => true,
                'message' => 'Satuan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Gagal menghapus: Satuan ini masih digunakan oleh data barang.');
        }
    }
}