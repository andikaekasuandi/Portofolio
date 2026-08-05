<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggajianModel extends Model
{
    protected $table            = 'penggajian';
    protected $primaryKey       = 'id_penggajian';
    protected $allowedFields    = [
        'kode_slip', 'id_karyawan', 'bulan', 'tahun', 'gaji_pokok',
        'tunjangan', 'uang_lembur', 'potongan', 'total_gaji', 'tanggal_gaji',
        'diproses_oleh', 'diproses_oleh_role',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
        'id_karyawan' => 'required|numeric',
        'bulan'       => 'required|numeric|greater_than[0]|less_than[13]',
        'tahun'       => 'required|numeric',
    ];

    public function getWithKaryawan($id = null)
    {
        $builder = $this->select('penggajian.*, karyawan.nama, karyawan.kode_karyawan, karyawan.nama_bank, karyawan.nomor_rekening, jabatan.nama_jabatan')
            ->join('karyawan', 'karyawan.id_karyawan = penggajian.id_karyawan')
            ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan');

        if ($id !== null) {
            return $builder->where('penggajian.id_penggajian', $id)->first();
        }

        return $builder->orderBy('penggajian.tanggal_gaji', 'DESC')->findAll();
    }

    /**
     * @param string|null $tipe 'admin' = hanya karyawan yang punya akun Admin,
     *                          'karyawan' = hanya karyawan biasa (bukan akun Admin),
     *                          null/'' = semua.
     */
    public function getFiltered(?int $bulan = null, ?int $tahun = null, ?string $tipe = null)
    {
        $builder = $this->select('penggajian.*, karyawan.nama, karyawan.kode_karyawan, karyawan.nama_bank, karyawan.nomor_rekening, jabatan.nama_jabatan')
            ->join('karyawan', 'karyawan.id_karyawan = penggajian.id_karyawan')
            ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan');

        if (! empty($bulan)) {
            $builder->where('penggajian.bulan', $bulan);
        }

        if (! empty($tahun)) {
            $builder->where('penggajian.tahun', $tahun);
        }

        if ($tipe === 'admin' || $tipe === 'karyawan') {
            $adminKaryawanIds = (new UserModel())->getAdminKaryawanIds();

            if ($tipe === 'admin') {
                if (empty($adminKaryawanIds)) {
                    // Belum ada karyawan yang punya akun Admin -> hasil kosong.
                    $builder->where('penggajian.id_karyawan', -1);
                } else {
                    $builder->whereIn('penggajian.id_karyawan', $adminKaryawanIds);
                }
            } elseif (! empty($adminKaryawanIds)) {
                $builder->whereNotIn('penggajian.id_karyawan', $adminKaryawanIds);
            }
        }

        return $builder->orderBy('penggajian.tanggal_gaji', 'DESC')->findAll();
    }

    public function countKaryawanDigaji(int $bulan, int $tahun): int
    {
        return $this->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->countAllResults();
    }

    public function sudahDiproses(int $idKaryawan, int $bulan, int $tahun): bool
    {
        return (bool) $this->where('id_karyawan', $idKaryawan)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }

    public function generateKodeSlip(int $bulan, int $tahun): string
    {
        $prefix = 'SLP' . $tahun . str_pad((string) $bulan, 2, '0', STR_PAD_LEFT);
        $last   = $this->like('kode_slip', $prefix, 'after')->orderBy('id_penggajian', 'DESC')->first();
        $nomor  = 1;
        if ($last) {
            $nomor = (int) substr($last['kode_slip'], -4) + 1;
        }

        return $prefix . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT);
    }
}