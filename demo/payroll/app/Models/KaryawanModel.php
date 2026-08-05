<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id_karyawan';
    protected $allowedFields    = [
        'kode_karyawan', 'nama', 'jenis_kelamin', 'no_hp', 'alamat',
        'tanggal_masuk', 'status', 'id_jabatan', 'nama_bank', 'nomor_rekening',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
        'id_karyawan'    => 'permit_empty|numeric',
        'kode_karyawan' => 'required|max_length[10]|is_unique[karyawan.kode_karyawan,id_karyawan,{id_karyawan}]',
        'nama'          => 'required|max_length[100]',
        'jenis_kelamin' => 'required|in_list[L,P]',
        'tanggal_masuk' => 'required|valid_date',
        'status'        => 'required|in_list[Tetap,Kontrak]',
        'id_jabatan'    => 'required|numeric',
        'nama_bank'     => 'permit_empty|max_length[50]',
        'nomor_rekening' => 'permit_empty|max_length[30]',
    ];

    public function generateKodeKaryawan(): string
    {
        $prefix = 'KRY';
        $last   = $this->like('kode_karyawan', $prefix, 'after')->orderBy('id_karyawan', 'DESC')->first();
        $nomor  = 1;
        if ($last) {
            $nomor = (int) substr($last['kode_karyawan'], strlen($prefix)) + 1;
        }

        return $prefix . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT);
    }

    public function getWithJabatan($id = null)
    {
        $builder = $this->select('karyawan.*, jabatan.nama_jabatan, jabatan.gaji_pokok, jabatan.tunjangan, jabatan.tarif_lembur')
            ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan');

        if ($id !== null) {
            return $builder->where('karyawan.id_karyawan', $id)->first();
        }

        return $builder->findAll();
    }
}
