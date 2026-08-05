<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JabatanAdministratorSeeder extends Seeder
{
    /**
     * Jabatan 'Administrator' dicek by-name di beberapa tempat (JabatanController::isKhususOwner(),
     * AkunAdminController::pindahkanKeJabatanAdministrator()) tapi belum pernah ada baris masternya.
     */
    public function run()
    {
        $sudahAda = $this->db->table('jabatan')->where('nama_jabatan', 'Administrator')->get()->getRowArray();
        if ($sudahAda) {
            return;
        }

        $this->db->table('jabatan')->insert([
            'kode_jabatan' => 'JBT004',
            'nama_jabatan' => 'Administrator',
            'gaji_pokok'   => 4000000,
            'tunjangan'    => 400000,
            'tarif_lembur' => 25000,
        ]);
    }
}
