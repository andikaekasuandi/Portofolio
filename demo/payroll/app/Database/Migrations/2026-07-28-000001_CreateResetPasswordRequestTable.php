<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResetPasswordRequestTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_request' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'catatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => "ENUM('Pending','Selesai','Ditolak')",
                'default'    => 'Pending',
            ],
            'diproses_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_request');
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('reset_password_request');
    }

    public function down()
    {
        $this->forge->dropTable('reset_password_request', true);
    }
}
