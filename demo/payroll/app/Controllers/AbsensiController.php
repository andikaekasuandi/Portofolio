<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\KaryawanModel;
use App\Models\LogAktivitasModel;

class AbsensiController extends BaseController
{
    protected AbsensiModel $model;

    public function __construct()
    {
        $this->model = new AbsensiModel();
    }

   public function index()
{
    $bulan = $this->request->getGet('bulan') ?? date('n');
    $tahun = $this->request->getGet('tahun') ?? date('Y');

    return view('absensi/index', [
        'data'  => $this->model->getWithKaryawan(null, $bulan, $tahun),
        'bulan' => $bulan,
        'tahun' => $tahun,
    ]);
}
    public function create()
    {
        $karyawanModel = new KaryawanModel();

        return view('absensi/form', [
            'absensi' => null,
            'karyawan' => $karyawanModel->findAll(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost(['id_karyawan', 'tanggal', 'status', 'jam_lembur']);

        if (strtotime($data['tanggal']) > strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('errors', ['tanggal' => 'Tanggal tidak boleh lebih dari hari ini.']);
        }

        if ($this->model->sudahAdaAbsensi((int) $data['id_karyawan'], $data['tanggal'])) {
            $karyawan = (new KaryawanModel())->find($data['id_karyawan']);

            return redirect()->back()->withInput()->with('error', "{$karyawan['nama']} sudah punya catatan absensi untuk tanggal {$data['tanggal']}. Edit data yang sudah ada kalau mau mengubah statusnya.");
        }

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        $karyawan = (new KaryawanModel())->find($data['id_karyawan']);
        (new LogAktivitasModel())->catat('Tambah', 'Absensi', "Mencatat absensi {$data['status']} untuk {$karyawan['nama']} tanggal {$data['tanggal']}.");

        return redirect()->to('/absensi')->with('success', 'Absensi berhasil dicatat.');
    }


    public function edit($id)
    {
        $karyawanModel = new KaryawanModel();
        $absensi       = $this->model->find($id);

        if (! $absensi) {
            return redirect()->to('/absensi')->with('error', 'Data tidak ditemukan.');
        }

        return view('absensi/form', [
            'absensi'  => $absensi,
            'karyawan' => $karyawanModel->findAll(),
        ]);
    }

    public function update($id)
    {
        $data = $this->request->getPost(['id_karyawan', 'tanggal', 'status', 'jam_lembur']);
        $data['id_absensi'] = $id;

        if (strtotime($data['tanggal']) > strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('errors', ['tanggal' => 'Tanggal tidak boleh lebih dari hari ini.']);
        }

        if ($this->model->sudahAdaAbsensi((int) $data['id_karyawan'], $data['tanggal'], (int) $id)) {
            $karyawan = (new KaryawanModel())->find($data['id_karyawan']);

            return redirect()->back()->withInput()->with('error', "{$karyawan['nama']} sudah punya catatan absensi lain untuk tanggal {$data['tanggal']}.");
        }

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        $karyawan = (new KaryawanModel())->find($data['id_karyawan']);
        (new LogAktivitasModel())->catat('Ubah', 'Absensi', "Mengubah absensi {$karyawan['nama']} tanggal {$data['tanggal']} menjadi {$data['status']}.");

        return redirect()->to('/absensi')->with('success', 'Absensi berhasil diperbarui.');
    }


    public function delete($id)
    {
        $absensi = $this->model->find($id);
        if (! $absensi) {
            return redirect()->to('/absensi')->with('error', 'Data tidak ditemukan.');
        }

        $karyawan = (new KaryawanModel())->find($absensi['id_karyawan']);

        $this->model->delete($id);

        (new LogAktivitasModel())->catat('Hapus', 'Absensi', "Menghapus absensi {$karyawan['nama']} tanggal {$absensi['tanggal']}.");

        return redirect()->to('/absensi')->with('success', 'Absensi berhasil dihapus.');
    }
}
