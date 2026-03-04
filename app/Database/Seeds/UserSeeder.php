<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'username'   => 'petugas' . $i,
                'nama'       => 'Petugas ' . $i,
                'password'   => password_hash('123456', PASSWORD_DEFAULT),
                'role'       => 'petugas',
                'is_active'  => 1,
            ];
        }

        $this->db->table('users')->insertBatch($data);
    }
}
