<?php

namespace App\Controllers;

use App\Models\JabatanModel;
use App\Models\LogAktivitasModel;

class JabatanController extends BaseController
{
    protected JabatanModel $model;

    public function __construct()
    {
        $this->model = new JabatanModel();
    }

    public function index()
    {
        return view('jabatan/index', ['data' => $this->model->findAll()]);
    }

    public function create()
    {
        return view('jabatan/form', ['jabatan' => null]);
    }

    public function store()
    {
        $data = $this->request->getPost(['kode_jabatan', 'nama_jabatan', 'gaji_pokok', 'tunjangan', 'tarif_lembur']);

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        (new LogAktivitasModel())->catat('Tambah', 'Jabatan', "Menambahkan jabatan {$data['nama_jabatan']} ({$data['kode_jabatan']}).");

        return redirect()->to('/jabatan')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jabatan = $this->model->find($id);
        if (! $jabatan) {
            return redirect()->to('/jabatan')->with('error', 'Data tidak ditemukan.');
        }

        if ($this->isKhususOwner($jabatan)) {
            return redirect()->to('/jabatan')->with('error', 'Jabatan Administrator hanya bisa diubah oleh Owner.');
        }

        return view('jabatan/form', ['jabatan' => $jabatan]);
    }

    public function update($id)
    {
        $jabatanLama = $this->model->find($id);
        if (! $jabatanLama) {
            return redirect()->to('/jabatan')->with('error', 'Data tidak ditemukan.');
        }

        if ($this->isKhususOwner($jabatanLama)) {
            return redirect()->to('/jabatan')->with('error', 'Jabatan Administrator hanya bisa diubah oleh Owner.');
        }

        $data = $this->request->getPost(['kode_jabatan', 'nama_jabatan', 'gaji_pokok', 'tunjangan', 'tarif_lembur']);
        $data['id_jabatan'] = $id;

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        (new LogAktivitasModel())->catat('Ubah', 'Jabatan', "Mengubah data jabatan {$data['nama_jabatan']} ({$data['kode_jabatan']}).");

        return redirect()->to('/jabatan')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $jabatan = $this->model->find($id);
        if (! $jabatan) {
            return redirect()->to('/jabatan')->with('error', 'Data tidak ditemukan.');
        }

        if ($this->isKhususOwner($jabatan)) {
            return redirect()->to('/jabatan')->with('error', 'Jabatan Administrator hanya bisa dihapus oleh Owner.');
        }

        $this->model->delete($id);

        (new LogAktivitasModel())->catat('Hapus', 'Jabatan', "Menghapus jabatan {$jabatan['nama_jabatan']} ({$jabatan['kode_jabatan']}).");

        return redirect()->to('/jabatan')->with('success', 'Jabatan berhasil dihapus.');
    }

    private function isKhususOwner(array $jabatan): bool
    {
        return $jabatan['nama_jabatan'] === 'Administrator' && session()->get('role') !== 'Owner';
    }
}