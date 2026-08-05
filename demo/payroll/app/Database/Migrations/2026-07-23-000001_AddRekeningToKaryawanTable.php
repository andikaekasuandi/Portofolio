<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRekeningToKaryawanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('karyawan', [
            'nama_bank' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'status',
            ],
            'nomor_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'nama_bank',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('karyawan', ['nama_bank', 'nomor_rekening']);
    }
}
