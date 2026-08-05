<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\JabatanModel;
use App\Models\KaryawanModel;
use App\Models\PenggajianModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        $karyawanModel   = new KaryawanModel();
        $absensiModel    = new AbsensiModel();
        $penggajianModel = new PenggajianModel();

        $data = ['role' => $role];

        if ($role === 'Owner') {
            $data['total_karyawan']  = $karyawanModel->countAllResults();
            $data['total_penggajian_bulan_ini'] = $penggajianModel
                ->where('MONTH(tanggal_gaji)', date('n'))
                ->where('YEAR(tanggal_gaji)', date('Y'))
                ->countAllResults();

            return view('dashboard/owner', $data);
        }

        if ($role === 'Karyawan') {
            $idKaryawan = session()->get('id_karyawan');
            $bulanIni   = (int) date('n');
            $tahunIni   = (int) date('Y');

            $data['karyawan']      = $idKaryawan ? $karyawanModel->getWithJabatan($idKaryawan) : null;
            $data['rekap_absensi'] = $idKaryawan ? $absensiModel->rekapBulanan((int) $idKaryawan, $bulanIni, $tahunIni) : null;
            $data['slip_terakhir'] = $idKaryawan ? $penggajianModel
                ->where('id_karyawan', $idKaryawan)
                ->orderBy('tanggal_gaji', 'DESC')
                ->first() : null;

            return view('dashboard/karyawan', $data);
        }

        // Admin
        $jabatanModel = new JabatanModel();

        $data['total_karyawan']   = $karyawanModel->countAllResults();
        $data['absensi_hari_ini'] = $absensiModel->where('tanggal', date('Y-m-d'))->countAllResults();
        $data['total_jabatan']    = $jabatanModel->countAllResults();

        // Total penggajian bulan berjalan (sum total_gaji)
        $penggajianBulanIni = $penggajianModel
            ->selectSum('total_gaji')
            ->where('bulan', (int) date('n'))
            ->where('tahun', (int) date('Y'))
            ->first();
        $data['penggajian_bulan_ini'] = $penggajianBulanIni['total_gaji'] ?? 0;

        return view('dashboard/admin', $data);
    }
}