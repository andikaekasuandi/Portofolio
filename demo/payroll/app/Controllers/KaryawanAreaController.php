<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\PenggajianModel;

class KaryawanAreaController extends BaseController
{
    public function absensiSaya()
    {
        $idKaryawan = session()->get('id_karyawan');
        if (! $idKaryawan) {
            return redirect()->to('/dashboard')->with('error', 'Akun Anda belum terhubung ke data karyawan. Hubungi Admin.');
        }

        $bulan = $this->request->getGet('bulan') ?? date('n');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        return view('absensi/saya', [
            'data'  => (new AbsensiModel())->getWithKaryawan($idKaryawan, $bulan, $tahun),
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    public function gajiSaya()
    {
        $idKaryawan = session()->get('id_karyawan');
        if (! $idKaryawan) {
            return redirect()->to('/dashboard')->with('error', 'Akun Anda belum terhubung ke data karyawan. Hubungi Admin.');
        }

        $data = (new PenggajianModel())
            ->select('penggajian.*, karyawan.nama, karyawan.kode_karyawan, karyawan.nama_bank, karyawan.nomor_rekening, jabatan.nama_jabatan')
            ->join('karyawan', 'karyawan.id_karyawan = penggajian.id_karyawan')
            ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan')
            ->where('penggajian.id_karyawan', $idKaryawan)
            ->orderBy('penggajian.tanggal_gaji', 'DESC')
            ->findAll();

        return view('penggajian/saya', ['data' => $data]);
    }

    public function cetakGajiSaya($id)
    {
        $idKaryawan = session()->get('id_karyawan');
        $slip       = (new PenggajianModel())->getWithKaryawan($id);

        if (! $slip || ! $idKaryawan || (int) $slip['id_karyawan'] !== (int) $idKaryawan) {
            return redirect()->to('/gaji-saya')->with('error', 'Slip gaji tidak ditemukan.');
        }

        return view('penggajian/slip', ['slip' => $slip]);
    }
}
