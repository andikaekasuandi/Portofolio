<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiprosesOlehToPenggajianTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('penggajian', [
            'diproses_oleh' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'tanggal_gaji',
            ],
            'diproses_oleh_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'diproses_oleh',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penggajian', ['diproses_oleh', 'diproses_oleh_role']);
    }
}
