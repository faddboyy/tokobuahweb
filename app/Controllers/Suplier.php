<?php

namespace App\Controllers;

use App\Models\SuplierModel;
use CodeIgniter\RESTful\ResourceController;

class Suplier extends ResourceController
{
    protected $modelName = 'App\Models\SuplierModel';
    protected $format    = 'json';

    // Menampilkan Halaman Utama
    public function index()
    {
        return view('master/suplier', ['title' => 'Master Suplier']);
    }

    // Ambil Data (GET)
    public function list()
    {
        return $this->respond([
            'status' => true,
            'data'   => $this->model->orderBy('id', 'DESC')->findAll()
        ]);
    }

    // Simpan Data Baru (POST)
    public function store()
    {
        $rules = [
            'nama'    => 'required|min_length[3]',
            'telepon' => 'required|numeric|min_length[10]',
            'email'   => 'permit_empty|valid_email',
            'alamat'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }

        $this->model->insert([
            'nama'    => $this->request->getVar('nama'),
            'alamat'  => $this->request->getVar('alamat'),
            'telepon' => $this->request->getVar('telepon'),
            'email'   => $this->request->getVar('email'),
        ]);

        return $this->respond(['status' => true, 'message' => 'Suplier berhasil ditambahkan']);
    }

    // Update Data (POST)
    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();

        $rules = [
            'nama'    => 'required|min_length[3]',
            'telepon' => 'required|numeric|min_length[10]',
            'email'   => 'permit_empty|valid_email',
            'alamat'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }

        $this->model->update($id, [
            'nama'    => $this->request->getVar('nama'),
            'alamat'  => $this->request->getVar('alamat'),
            'telepon' => $this->request->getVar('telepon'),
            'email'   => $this->request->getVar('email'),
        ]);

        return $this->respond(['status' => true, 'message' => 'Data suplier diperbarui']);
    }

    // Hapus Data (DELETE)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();

        $this->model->delete($id);
        return $this->respond(['status' => true, 'message' => 'Suplier telah dihapus']);
    }
}