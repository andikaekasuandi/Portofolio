<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\JabatanModel;
use App\Models\KaryawanModel;
use App\Models\LogAktivitasModel;
use App\Models\ResetPasswordRequestModel;
use App\Models\UserModel;

class AkunAdminController extends BaseController
{
    protected UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $data = $this->model->getAdminsWithKaryawan();

        return view('akun_admin/index', [
            'data'                  => $data,
            'resetPasswordRequests' => (new ResetPasswordRequestModel())->getPendingWithUser('Admin'),
        ]);
    }

    public function create()
    {
        $karyawanModel = new KaryawanModel();

        return view('akun_admin/form', [
            'akun'     => null,
            'karyawan' => $karyawanModel->orderBy('nama', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        $data = [
            'username'    => $this->request->getPost('username'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => 'Admin',
            'id_karyawan' => $this->request->getPost('id_karyawan') ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        if (! empty($data['id_karyawan'])) {
            $absensiModel = new AbsensiModel();
            if (! $absensiModel->sudahAdaAbsensi((int) $data['id_karyawan'])) {
                $karyawan = (new KaryawanModel())->find($data['id_karyawan']);
                $absensiModel->generateDummySejakMasuk((int) $data['id_karyawan'], $karyawan['tanggal_masuk'] ?? null);
            }

            $this->pindahkanKeJabatanAdministrator((int) $data['id_karyawan']);
        }

        (new LogAktivitasModel())->catat('Tambah', 'Akun Admin', "Membuat akun admin {$data['username']}.");

        return redirect()->to('/akun-admin')->with('success', 'Akun admin berhasil dibuat.');
    }

    public function edit($id)
    {
        $akun = $this->model->find($id);
        if (! $akun || $akun['role'] !== 'Admin') {
            return redirect()->to('/akun-admin')->with('error', 'Data tidak ditemukan.');
        }

        return view('akun_admin/form', ['akun' => $akun]);
    }

    public function update($id)
    {
        $data = [
            'id_user'  => $id,
            'username' => $this->request->getPost('username'),
        ];

        $password = $this->request->getPost('password');
        if (! empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        (new LogAktivitasModel())->catat('Ubah', 'Akun Admin', "Mengubah akun admin {$data['username']}.");

        return redirect()->to('/akun-admin')->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function delete($id)
    {
        $akun = $this->model->find($id);
        if (! $akun || $akun['role'] !== 'Admin') {
            return redirect()->to('/akun-admin')->with('error', 'Data tidak ditemukan.');
        }

        $this->model->delete($id);

        (new LogAktivitasModel())->catat('Hapus', 'Akun Admin', "Menghapus akun admin {$akun['username']}.");

        return redirect()->to('/akun-admin')->with('success', 'Akun admin berhasil dihapus.');
    }

    public function konfirmasiResetPassword($idRequest)
    {
        [$resetRequest, $akun, $error] = $this->ambilPermintaanReset($idRequest, 'Admin');
        if ($error) {
            return redirect()->to('/akun-admin')->with('error', $error);
        }

        return view('akun_admin/reset_password', ['akun' => $akun, 'resetRequest' => $resetRequest]);
    }

    public function simpanResetPassword($idRequest)
    {
        [$resetRequest, $akun, $error] = $this->ambilPermintaanReset($idRequest, 'Admin');
        if ($error) {
            return redirect()->to('/akun-admin')->with('error', $error);
        }

        $password = (string) $this->request->getPost('password');
        if (strlen($password) < 6) {
            return redirect()->back()->with('error', 'Password minimal 6 karakter.');
        }

        $this->model->save([
            'id_user'  => $akun['id_user'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        (new ResetPasswordRequestModel())->update($resetRequest['id_request'], [
            'status'        => 'Selesai',
            'diproses_oleh' => session()->get('username'),
            'processed_at'  => date('Y-m-d H:i:s'),
        ]);

        (new LogAktivitasModel())->catat('Reset Password', 'Akun Admin', "Mereset password akun admin {$akun['username']}.");

        return redirect()->to('/akun-admin')->with('success', "Password akun {$akun['username']} berhasil direset.");
    }

    public function tolakResetPassword($idRequest)
    {
        [$resetRequest, $akun, $error] = $this->ambilPermintaanReset($idRequest, 'Admin');
        if ($error) {
            return redirect()->to('/akun-admin')->with('error', $error);
        }

        (new ResetPasswordRequestModel())->update($resetRequest['id_request'], [
            'status'        => 'Ditolak',
            'diproses_oleh' => session()->get('username'),
            'processed_at'  => date('Y-m-d H:i:s'),
        ]);

        (new LogAktivitasModel())->catat('Tolak Reset Password', 'Akun Admin', "Menolak permintaan reset password akun admin {$akun['username']}.");

        return redirect()->to('/akun-admin')->with('success', 'Permintaan reset password ditolak.');
    }

    /**
     * @return array{0: array|null, 1: array|null, 2: string|null} [resetRequest, akun, pesanError]
     */
    private function ambilPermintaanReset($idRequest, string $role): array
    {
        $resetRequest = (new ResetPasswordRequestModel())->find($idRequest);
        if (! $resetRequest || $resetRequest['status'] !== 'Pending') {
            return [null, null, 'Permintaan tidak ditemukan atau sudah diproses.'];
        }

        $akun = $this->model->find($resetRequest['id_user']);
        if (! $akun || $akun['role'] !== $role) {
            return [null, null, 'Data tidak ditemukan.'];
        }

        return [$resetRequest, $akun, null];
    }

    private function pindahkanKeJabatanAdministrator(int $idKaryawan): void
    {
        $jabatanModel  = new JabatanModel();
        $karyawanModel = new KaryawanModel();

        $jabatanAdmin = $jabatanModel->where('nama_jabatan', 'Administrator')->first();
        if (! $jabatanAdmin) {
            return; // jabatan Administrator belum ada di master data jabatan
        }

        $karyawan = $karyawanModel->find($idKaryawan);
        if (! $karyawan || (int) $karyawan['id_jabatan'] === (int) $jabatanAdmin['id_jabatan']) {
            return; // sudah Administrator, gak perlu diubah lagi
        }

        $karyawanModel->update($idKaryawan, ['id_jabatan' => $jabatanAdmin['id_jabatan']]);

        (new LogAktivitasModel())->catat(
            'Ubah',
            'Jabatan Karyawan',
            "Jabatan {$karyawan['nama']} otomatis diubah ke Administrator karena diangkat menjadi akun Admin."
        );
    }
}