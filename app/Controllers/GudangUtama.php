<?php

namespace App\Controllers;

use App\Models\GudangUtamaModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class GudangUtama extends ResourceController
{
    protected $modelName = 'App\Models\GudangUtamaModel';
    protected $format    = 'json';

    public function index()
    {
        return view('master/gudang_utama', ['title' => 'Master Gudang']);
    }

    public function list()
    {
        $db = \Config\Database::connect();

        $data = $db->table('gudang_utama g')
            ->select('g.*, u.nama as mandor_nama')
            ->join('users u', 'u.id = g.mandor_id', 'left')
            ->orderBy('g.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function mandorList()
    {
        $userModel = new UserModel();

        $data = $userModel
            ->select('id, nama')
            ->where('is_active', 1)
            ->findAll();

        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]|is_unique[gudang_utama.nama]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $this->model->insert([
            'nama'      => $this->request->getVar('nama'),
            'mandor_id' => $this->request->getVar('mandor_id')
        ]);

        return $this->respond([
            'status' => true,
            'message' => 'Gudang berhasil ditambahkan'
        ]);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound();
        }

        $rules = [
            'nama' => "required|min_length[3]|is_unique[gudang_utama.nama,id,$id]"
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $this->model->update($id, [
            'nama'      => $this->request->getVar('nama'),
            'mandor_id' => $this->request->getVar('mandor_id')
        ]);

        return $this->respond([
            'status' => true,
            'message' => 'Gudang berhasil diperbarui'
        ]);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);

        return $this->respond([
            'status' => true,
            'message' => 'Gudang berhasil dihapus'
        ]);
    }
}