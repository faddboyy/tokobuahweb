<?php

namespace App\Controllers;

use App\Models\GudangUtamaModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        return view('login');
    }

    public function process()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[4]',
        ];

        if (! $this->validate($rules)) {
            return $this->respond([
                'status' => false,
                'errors' => $this->validator->getErrors(), // error per field
                'message' => 'Validasi gagal'
            ], 422);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        // ❌ user tidak ditemukan
        if (!$user) {
            return $this->respond([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // ❌ akun tidak aktif
        if (!$user['is_active']) {
            return $this->respond([
                'status' => false,
                'message' => 'Akun tidak aktif'
            ], 403);
        }

        // ❌ password salah
        if (!password_verify($password, $user['password'])) {
            return $this->respond([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        $gudangModel = new GudangUtamaModel();
        $gudang = $gudangModel
            ->where('mandor_id', $user['id'])
            ->first();

        // ✅ set session
        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'nama'      => $user['nama'],
            'role'      => $user['role'],
            'cabang_id' => $user['cabang_id'] ?? null,
            'gudang_id' => $gudang['id'] ?? null,
            'isLoggedIn' => true,
        ]);

        return $this->respond([
            'status'   => true,
            'message'  => 'Login berhasil',
            'redirect' => base_url('/dashboard')
        ]);
    }


    public function logout()
    {
        $userId = session()->get('user_id');
        session()->destroy();

       return redirect()->to('/');
    }
}
