<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogAktivitasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'aksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'modul' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addPrimaryKey('id_log');
        $this->forge->addKey('id_user');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'SET NULL');
        $this->forge->createTable('log_aktivitas');
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas', true);
    }
}
