<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id_absensi';
    protected $allowedFields    = [
        'id_karyawan', 'tanggal', 'status', 'jam_lembur',
    ];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
        'id_karyawan' => 'required|numeric',
        'tanggal'     => 'required|valid_date',
        'status'      => 'required|in_list[Hadir,Izin,Sakit,Alpha]',
        'jam_lembur'  => 'permit_empty|numeric',
    ];

    public function getWithKaryawan($idKaryawan = null, $bulan = null, $tahun = null)
    {
        $builder = $this->db->table('absensi')
            ->select('absensi.*, karyawan.nama, karyawan.kode_karyawan')
            ->join('karyawan', 'karyawan.id_karyawan = absensi.id_karyawan');

        if ($idKaryawan !== null) {
            $builder->where('absensi.id_karyawan', $idKaryawan);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(absensi.tanggal)', (int)$bulan, false);
        }

        if (!empty($tahun)) {
            $builder->where('YEAR(absensi.tanggal)', (int)$tahun, false);
        }

        return $builder->get()->getResultArray();
    }

    public function rekapBulanan(int $idKaryawan, int $bulan, int $tahun): array
    {
        $rows = $this->where('id_karyawan', $idKaryawan)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->findAll();

        $rekap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0, 'total_lembur' => 0];
        foreach ($rows as $r) {
            $rekap[$r['status']]++;
            $rekap['total_lembur'] += (int) $r['jam_lembur'];
        }

        return $rekap;
    }

    /**
     * Dipanggil dengan 1 argumen (dari AkunAdminController, buat cek apakah karyawan
     * ini sudah pernah dibuatkan absensi dummy sama sekali) -> cek semua tanggal.
     *
     * Dipanggil dengan $tanggal (dari AbsensiController::store/update) -> cek khusus
     * tanggal itu, dan $kecualiId dipakai supaya record yang sedang diedit tidak
     * dianggap bentrok dengan dirinya sendiri.
     */
    public function sudahAdaAbsensi(int $idKaryawan, ?string $tanggal = null, ?int $kecualiId = null): bool
    {
        if ($tanggal === null) {
            return $this->where('id_karyawan', $idKaryawan)->countAllResults() > 0;
        }

        $builder = $this->where('id_karyawan', $idKaryawan)
            ->where('tanggal', $tanggal);

        if ($kecualiId !== null) {
            $builder = $builder->where('id_absensi !=', $kecualiId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Generate absensi dummy untuk karyawan sejak tanggal_masuk sampai hari ini,
     * lintas bulan (bukan cuma bulan berjalan).
     */
    public function generateDummySejakMasuk(int $idKaryawan, ?string $tanggalMasuk = null): void
    {
        $hariIniTimestamp = strtotime(date('Y-m-d'));

        // Kalau tanggal masuk tidak diisi, fallback ke hari ini saja
        $mulaiTimestamp = $tanggalMasuk ? strtotime($tanggalMasuk) : $hariIniTimestamp;

        if ($mulaiTimestamp > $hariIniTimestamp) {
            return; // karyawan baru masuk di masa mendatang, belum ada absensi untuk diisi
        }

        for ($timestamp = $mulaiTimestamp; $timestamp <= $hariIniTimestamp; $timestamp = strtotime('+1 day', $timestamp)) {
            if ((int) date('N', $timestamp) >= 6) {
                continue; // lewati Sabtu & Minggu
            }

            $acak = mt_rand(1, 100);
            if ($acak <= 85) {
                $status = 'Hadir';
            } elseif ($acak <= 90) {
                $status = 'Izin';
            } elseif ($acak <= 95) {
                $status = 'Sakit';
            } else {
                $status = 'Alpha';
            }

            $jamLembur = ($status === 'Hadir' && mt_rand(1, 100) <= 30) ? mt_rand(1, 4) : 0;

            $this->insert([
                'id_karyawan' => $idKaryawan,
                'tanggal'     => date('Y-m-d', $timestamp),
                'status'      => $status,
                'jam_lembur'  => $jamLembur,
            ]);
        }
    }
}