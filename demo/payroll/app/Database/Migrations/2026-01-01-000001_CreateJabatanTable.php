<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJabatanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jabatan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'nama_jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'gaji_pokok' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'tunjangan' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'tarif_lembur' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
        ]);
        $this->forge->addPrimaryKey('id_jabatan');
        $this->forge->addUniqueKey('kode_jabatan');
        $this->forge->createTable('jabatan');
    }

    public function down()
    {
        $this->forge->dropTable('jabatan', true);
    }
}
