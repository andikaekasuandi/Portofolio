<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenggajianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penggajian' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_slip' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'id_karyawan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bulan' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
            ],
            'tahun' => [
                'type' => 'YEAR',
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
            'uang_lembur' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'total_gaji' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'tanggal_gaji' => [
                'type' => 'DATE',
            ],
        ]);
        $this->forge->addPrimaryKey('id_penggajian');
        $this->forge->addUniqueKey('kode_slip');
        $this->forge->addForeignKey('id_karyawan', 'karyawan', 'id_karyawan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penggajian');
    }

    public function down()
    {
        $this->forge->dropTable('penggajian', true);
    }
}
