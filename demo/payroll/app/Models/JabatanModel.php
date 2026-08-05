<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $table            = 'jabatan';
    protected $primaryKey       = 'id_jabatan';
    protected $allowedFields    = [
        'kode_jabatan', 'nama_jabatan', 'gaji_pokok', 'tunjangan', 'tarif_lembur',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
    'id_jabatan'   => 'permit_empty|numeric',
    'kode_jabatan' => 'required|max_length[10]|is_unique[jabatan.kode_jabatan,id_jabatan,{id_jabatan}]',
    'nama_jabatan' => 'required|max_length[50]',
    'gaji_pokok'   => 'required|numeric',
    'tunjangan'    => 'permit_empty|numeric',
    'tarif_lembur' => 'permit_empty|numeric',
];
}
