<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKaryawanRoleBackToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'role' => [
                'name' => 'role',
                'type' => "ENUM('Owner','Admin','Karyawan')",
            ],
        ]);
    }

    public function down()
    {
        $this->db->table('users')->where('role', 'Karyawan')->delete();

        $this->forge->modifyColumn('users', [
            'role' => [
                'name' => 'role',
                'type' => "ENUM('Owner','Admin')",
            ],
        ]);
    }
}
