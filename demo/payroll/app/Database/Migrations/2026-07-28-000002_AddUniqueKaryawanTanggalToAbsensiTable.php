<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueKaryawanTanggalToAbsensiTable extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE absensi ADD UNIQUE KEY uq_absensi_karyawan_tanggal (id_karyawan, tanggal)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE absensi DROP INDEX uq_absensi_karyawan_tanggal');
    }
}
