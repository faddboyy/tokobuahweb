<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\CabangModel;
use CodeIgniter\RESTful\ResourceController;

class Customer extends ResourceController
{
    protected $modelName = 'App\Models\CustomerModel';
    protected $format    = 'json';

    public function index()
    {
        return view('master/customer', ['title' => 'Master Customer']);
    }

    public function list()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('customer');
        $builder->select('customer.*, cabang.nama as nama_cabang, users.nama as pembuat');
        $builder->join('cabang', 'cabang.id = customer.cabang_id', 'left');
        $builder->join('users', 'users.id = customer.added_by', 'left');
        $builder->orderBy('customer.id', 'DESC');

        return $this->respond([
            'status' => true,
            'data'   => $builder->get()->getResultArray(),
            'listCabang' => (new CabangModel())->findAll()
        ]);
    }

    public function store()
    {
        $rules = [
            'nama'    => 'required|min_length[3]',
            'telepon' => 'required|numeric',
            'alamat'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }

        $this->model->insert([
            'nama'      => $this->request->getVar('nama'),
            'alamat'    => $this->request->getVar('alamat'),
            'telepon'   => $this->request->getVar('telepon'),
            'cabang_id' => $this->request->getVar('cabang_id') ?: (session()->get('role') === 'petugas' ? session()->get('cabang_id') : null),
            'added_by'  => session()->get('user_id'),
        ]);

        return $this->respond(['status' => true, 'message' => 'Customer berhasil ditambahkan']);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();

        $rules = [
            'nama'    => 'required|min_length[3]',
            'telepon' => 'required|numeric',
            'alamat'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }

        $cabang_id = $this->request->getVar('cabang_id');
        $this->model->update($id, [
            'nama'      => $this->request->getVar('nama'),
            'alamat'    => $this->request->getVar('alamat'),
            'telepon'   => $this->request->getVar('telepon'),
            'cabang_id' => $cabang_id ?: $this->model->find($id)['cabang_id'],
        ]);

        return $this->respond(['status' => true, 'message' => 'Data customer diperbarui']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();
        $this->model->delete($id);
        return $this->respond(['status' => true, 'message' => 'Customer telah dihapus']);
    }
}
