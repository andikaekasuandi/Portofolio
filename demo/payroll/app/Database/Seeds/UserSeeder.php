<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Akun default. GANTI PASSWORD setelah login pertama kali!
        $data = [
            [
                'username'    => 'owner',
                'password'    => password_hash('owner123', PASSWORD_DEFAULT),
                'role'        => 'Owner',
                'id_karyawan' => null,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'username'    => 'admin',
                'password'    => password_hash('admin123', PASSWORD_DEFAULT),
                'role'        => 'Admin',
                'id_karyawan' => null,
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
