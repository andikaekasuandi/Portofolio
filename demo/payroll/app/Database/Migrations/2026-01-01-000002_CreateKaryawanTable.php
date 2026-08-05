<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_karyawan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_karyawan' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jenis_kelamin' => [
                'type'       => "ENUM('L','P')",
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type' => "ENUM('Tetap','Kontrak')",
            ],
            'id_jabatan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_karyawan');
        $this->forge->addUniqueKey('kode_karyawan');
        $this->forge->addForeignKey('id_jabatan', 'jabatan', 'id_jabatan', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('karyawan', true);
    }
}
