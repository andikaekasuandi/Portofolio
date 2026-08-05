<?php

namespace App\Controllers;

use App\Models\PenggajianModel;

class LaporanController extends BaseController
{
    public function penggajian()
    {
        $model = new PenggajianModel();
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun') ?: 2026;

        $builder = $model->select('penggajian.*, karyawan.nama, karyawan.kode_karyawan, jabatan.nama_jabatan')
            ->join('karyawan', 'karyawan.id_karyawan = penggajian.id_karyawan')
            ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan');

        if ($bulan) {
            $builder->where('penggajian.bulan', $bulan);
        }
        if ($tahun) {
            $builder->where('penggajian.tahun', $tahun);
        }

        $data = $builder->orderBy('penggajian.tahun', 'DESC')
            ->orderBy('penggajian.bulan', 'DESC')
            ->findAll();

        $totalGaji = array_sum(array_column($data, 'total_gaji'));

        // Rata-rata gaji untuk bulan yang difilter (cuma dihitung kalau bulan dipilih)
        $rataRataBulan = $bulan ? ($totalGaji > 0 && count($data) > 0 ? $totalGaji / count($data) : 0) : null;

        // Rata-rata gaji satu tahun penuh, terlepas dari filter bulan
        $dataTahun     = (new PenggajianModel())->where('tahun', $tahun)->findAll();
        $totalGajiTahun = array_sum(array_column($dataTahun, 'total_gaji'));
        $rataRataTahun = count($dataTahun) > 0 ? $totalGajiTahun / count($dataTahun) : 0;

        return view('laporan/penggajian', [
            'data'          => $data,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'totalGaji'     => $totalGaji,
            'rataRataBulan' => $rataRataBulan,
            'rataRataTahun' => $rataRataTahun,
        ]);
    }
}
