<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $allowedFields    = [
        'username', 'password', 'role', 'id_karyawan', 'created_at',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
    'id_user'     => 'permit_empty|integer',
    'username'    => 'required|max_length[50]|is_unique[users.username,id_user,{id_user}]',
    'role'        => 'permit_empty|in_list[Owner,Admin,Karyawan]',
    'id_karyawan' => 'permit_empty|is_not_unique[karyawan.id_karyawan]',
];

    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    public function getAdminsWithKaryawan()
    {
        return $this->select('users.*, karyawan.nama as nama_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = users.id_karyawan', 'left')
            ->where('users.role', 'Admin')
            ->findAll();
    }

    public function getAdminKaryawanIds(): array
    {
        return array_map('intval', array_column(
            $this->select('id_karyawan')
                ->where('role', 'Admin')
                ->where('id_karyawan IS NOT NULL')
                ->findAll(),
            'id_karyawan'
        ));
    }

    public function getKaryawanAccountsWithKaryawan()
    {
        return $this->select('users.*, karyawan.nama as nama_karyawan, karyawan.kode_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = users.id_karyawan', 'left')
            ->where('users.role', 'Karyawan')
            ->findAll();
    }

    /**
     * Semua id_karyawan yang sudah punya akun login (Admin maupun Karyawan),
     * dipakai supaya satu karyawan nggak bisa dibuatkan akun dobel.
     */
    public function getKaryawanIdsWithAccount(): array
    {
        return array_map('intval', array_column(
            $this->select('id_karyawan')
                ->where('id_karyawan IS NOT NULL')
                ->whereIn('role', ['Admin', 'Karyawan'])
                ->findAll(),
            'id_karyawan'
        ));
    }
}
