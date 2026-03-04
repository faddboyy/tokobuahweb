<?php

namespace App\Controllers;

use App\Models\CabangModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Cabang extends ResourceController
{
    protected $modelName = 'App\Models\CabangModel';
    protected $format    = 'json';

    public function index()
    {
        return view('master/cabang', ['title' => 'Master Cabang']);
    }

    public function list()
    {
        $db = \Config\Database::connect();
        $data = $db->table('cabang c')
            ->select('c.*, GROUP_CONCAT(u.nama SEPARATOR ", ") as petugas')
            ->join('users u', 'u.cabang_id = c.id', 'left')
            ->groupBy('c.id')
            ->orderBy('c.id', 'DESC')
            ->get()->getResultArray();

        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function petugasList()
    {
        $userModel = new UserModel();
        $data = $userModel->select('id, nama, cabang_id')
            ->where('role', 'petugas')
            ->findAll();
        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        $rules = [
            'nama' => 'required|min_length[3]|is_unique[cabang.nama]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $id = $this->model->insert(['nama' => $this->request->getVar('nama')]);

        if ($id) {
            $petugasIds = $this->request->getVar('petugas_ids') ?? [];
            if (!empty($petugasIds)) {
                $userModel = new UserModel();
                $userModel->whereIn('id', $petugasIds)->set(['cabang_id' => $id])->update();
            }
        }

        return $this->respond(['status' => true, 'message' => 'Cabang berhasil ditambahkan']);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();

        $rules = [
            'nama' => "required|min_length[3]|is_unique[cabang.nama,id,$id]"
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $this->model->update($id, ['nama' => $this->request->getVar('nama')]);

        $userModel = new UserModel();
        $userModel->where('cabang_id', $id)->set(['cabang_id' => null])->update();

        $petugasIds = $this->request->getVar('petugas_ids') ?? [];
        if (!empty($petugasIds)) {
            $userModel->whereIn('id', $petugasIds)->set(['cabang_id' => $id])->update();
        }

        return $this->respond(['status' => true, 'message' => 'Cabang berhasil diperbarui']);
    }

    public function delete($id = null)
    {
        $userModel = new UserModel();
        $userModel->where('cabang_id', $id)->set(['cabang_id' => null])->update();
        $this->model->delete($id);
        return $this->respond(['status' => true, 'message' => 'Cabang berhasil dihapus']);
    }
}
