<?php

namespace App\Controllers;

use App\Models\JenisModel;
use CodeIgniter\RESTful\ResourceController;

class Jenis extends ResourceController
{
    protected $modelName = 'App\Models\JenisModel';
    protected $format    = 'json';

    /**
     * Mengambil semua data jenis untuk dropdown/list inline
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
     * Menyimpan jenis baru (Inline dari Modal Barang)
     */
    public function store()
    {
        $nama = $this->request->getVar('nama');

        if (empty($nama)) {
            return $this->fail('Nama jenis tidak boleh kosong');
        }

        $id = $this->model->insert([
            'nama' => $nama
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Jenis baru berhasil ditambahkan',
            'id'      => $id
        ]);
    }

    /**
     * Menghapus jenis (Inline dari Modal Barang)
     */
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Jenis tidak ditemukan');
        }

        // Catatan: Jika ada foreign key constraint, ini akan gagal jika jenis sudah dipakai barang
        try {
            $this->model->delete($id);
            return $this->respond([
                'status'  => true,
                'message' => 'Jenis berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->fail('Gagal menghapus: Jenis ini masih digunakan oleh data barang.');
        }
    }
}