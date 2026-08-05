<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AkunKaryawanDummySeeder extends Seeder
{
    /**
     * Membuatkan akun login (role Karyawan) untuk semua karyawan yang belum
     * punya akun login sama sekali (Admin maupun Karyawan). Username = kode_karyawan
     * huruf kecil, password dummy '123456' untuk semua akun (GANTI setelah login pertama!).
     */
    public function run()
    {
        $karyawan = $this->db->table('karyawan')->select('id_karyawan, kode_karyawan')->get()->getResultArray();

        $sudahPunyaAkun = array_column(
            $this->db->table('users')
                ->select('id_karyawan')
                ->where('id_karyawan IS NOT NULL')
                ->whereIn('role', ['Admin', 'Karyawan'])
                ->get()
                ->getResultArray(),
            'id_karyawan'
        );
        $sudahPunyaAkun = array_map('intval', $sudahPunyaAkun);

        $passwordHash = password_hash('123456', PASSWORD_DEFAULT);
        $now          = date('Y-m-d H:i:s');

        $data = [];
        foreach ($karyawan as $k) {
            if (in_array((int) $k['id_karyawan'], $sudahPunyaAkun, true)) {
                continue;
            }

            $data[] = [
                'username'    => strtolower($k['kode_karyawan']),
                'password'    => $passwordHash,
                'role'        => 'Karyawan',
                'id_karyawan' => $k['id_karyawan'],
                'created_at'  => $now,
            ];
        }

        if ($data === []) {
            return;
        }

        $this->db->table('users')->insertBatch($data);
    }
}
