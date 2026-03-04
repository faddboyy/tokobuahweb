<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    // Menampilkan Halaman Utama
    public function index()
    {
        return view('master/user', ['title' => 'Master User']);
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
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'nama'     => 'required|min_length[3]',
            'password' => 'required|min_length[4]',
            'role'     => 'required|in_list[owner,admin,petugas]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }

        $this->model->insert([
            'username'  => $this->request->getVar('username'),
            'nama'      => $this->request->getVar('nama'),
            'password'  => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'      => $this->request->getVar('role'),
            'is_active' => $this->request->getVar('is_active') ? 1 : 0,
        ]);

        return $this->respond(['status' => true, 'message' => 'User berhasil ditambahkan']);
    }

    // Update Data (POST)
    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();

        $rules = [
            'username' => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'nama'     => 'required|min_length[3]',
            'role'     => 'required|in_list[owner,admin,petugas]'
        ];

        if (!$this->validate($rules)) {
            return $this->respond(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }

        $data = [
            'username'  => $this->request->getVar('username'),
            'nama'      => $this->request->getVar('nama'),
            'role'      => $this->request->getVar('role'),
            'is_active' => $this->request->getVar('is_active') ? 1 : 0,
        ];

        $pass = $this->request->getVar('password');
        if (!empty($pass)) {
            $data['password'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $this->model->update($id, $data);
        return $this->respond(['status' => true, 'message' => 'User berhasil diperbarui']);
    }

    // Hapus Data (DELETE)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound();

        $this->model->delete($id);
        return $this->respond(['status' => true, 'message' => 'User telah dihapus']);
    }

    // Toggle Status (POST)
    public function toggle($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) return $this->failNotFound();

        $this->model->update($id, [
            'is_active' => $user['is_active'] == 1 ? 0 : 1
        ]);

        return $this->respond(['status' => true, 'message' => 'Status user diperbarui']);
    }
}
