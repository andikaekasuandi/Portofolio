<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveKaryawanRoleFromUsersTable extends Migration
{
    public function up()
    {
        $this->db->table('users')->where('role', 'Karyawan')->delete();

        $this->forge->modifyColumn('users', [
            'role' => [
                'name' => 'role',
                'type' => "ENUM('Owner','Admin')",
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('users', [
            'role' => [
                'name' => 'role',
                'type' => "ENUM('Owner','Admin','Karyawan')",
            ],
        ]);
    }
}
