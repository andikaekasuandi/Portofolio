<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\LogAktivitasModel;
use App\Models\ResetPasswordRequestModel;
use App\Models\UserModel;

class AkunKaryawanController extends BaseController
{
    protected UserModel $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $resetPasswordRequests = session()->get('role') === 'Admin'
            ? (new ResetPasswordRequestModel())->getPendingWithUser('Karyawan')
            : [];

        return view('akun_karyawan/index', [
            'data'                  => $this->model->getKaryawanAccountsWithKaryawan(),
            'resetPasswordRequests' => $resetPasswordRequests,
        ]);
    }

    public function create()
    {
        $karyawanModel  = new KaryawanModel();
        $sudahPunyaAkun = $this->model->getKaryawanIdsWithAccount();

        $karyawanTersedia = array_values(array_filter(
            $karyawanModel->orderBy('nama', 'ASC')->findAll(),
            static fn (array $k): bool => ! in_array((int) $k['id_karyawan'], $sudahPunyaAkun, true)
        ));

        return view('akun_karyawan/form', [
            'akun'     => null,
            'karyawan' => $karyawanTersedia,
        ]);
    }

    public function store()
    {
        $idKaryawan = $this->request->getPost('id_karyawan');

        if (empty($idKaryawan)) {
            return redirect()->back()->withInput()->with('error', 'Pilih karyawan yang akan dibuatkan akun login.');
        }

        if (in_array((int) $idKaryawan, $this->model->getKaryawanIdsWithAccount(), true)) {
            return redirect()->back()->withInput()->with('error', 'Karyawan ini sudah memiliki akun login.');
        }

        $data = [
            'username'    => $this->request->getPost('username'),
            'password'    => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => 'Karyawan',
            'id_karyawan' => (int) $idKaryawan,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        $karyawan = (new KaryawanModel())->find($data['id_karyawan']);

        (new LogAktivitasModel())->catat(
            'Tambah',
            'Akun Karyawan',
            "Membuat akun login untuk karyawan {$karyawan['nama']} ({$data['username']})."
        );

        return redirect()->to('/akun-karyawan')->with('success', 'Akun karyawan berhasil dibuat.');
    }

    public function edit($id)
    {
        $akun = $this->model->find($id);
        if (! $akun || $akun['role'] !== 'Karyawan') {
            return redirect()->to('/akun-karyawan')->with('error', 'Data tidak ditemukan.');
        }

        return view('akun_karyawan/form', [
            'akun'            => $akun,
            'karyawanTerkait' => $akun['id_karyawan'] ? (new KaryawanModel())->find($akun['id_karyawan']) : null,
        ]);
    }

    public function update($id)
    {
        $akun = $this->model->find($id);
        if (! $akun || $akun['role'] !== 'Karyawan') {
            return redirect()->to('/akun-karyawan')->with('error', 'Data tidak ditemukan.');
        }

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

        (new LogAktivitasModel())->catat('Ubah', 'Akun Karyawan', "Mengubah akun karyawan {$data['username']}.");

        return redirect()->to('/akun-karyawan')->with('success', 'Akun karyawan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $akun = $this->model->find($id);
        if (! $akun || $akun['role'] !== 'Karyawan') {
            return redirect()->to('/akun-karyawan')->with('error', 'Data tidak ditemukan.');
        }

        $this->model->delete($id);

        (new LogAktivitasModel())->catat('Hapus', 'Akun Karyawan', "Menghapus akun karyawan {$akun['username']}.");

        return redirect()->to('/akun-karyawan')->with('success', 'Akun karyawan berhasil dihapus.');
    }

    public function konfirmasiResetPassword($idRequest)
    {
        [$resetRequest, $akun, $error] = $this->ambilPermintaanReset($idRequest);
        if ($error) {
            return redirect()->to('/akun-karyawan')->with('error', $error);
        }

        return view('akun_karyawan/reset_password', ['akun' => $akun, 'resetRequest' => $resetRequest]);
    }

    public function simpanResetPassword($idRequest)
    {
        [$resetRequest, $akun, $error] = $this->ambilPermintaanReset($idRequest);
        if ($error) {
            return redirect()->to('/akun-karyawan')->with('error', $error);
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

        (new LogAktivitasModel())->catat('Reset Password', 'Akun Karyawan', "Mereset password akun karyawan {$akun['username']}.");

        return redirect()->to('/akun-karyawan')->with('success', "Password akun {$akun['username']} berhasil direset.");
    }

    public function tolakResetPassword($idRequest)
    {
        [$resetRequest, $akun, $error] = $this->ambilPermintaanReset($idRequest);
        if ($error) {
            return redirect()->to('/akun-karyawan')->with('error', $error);
        }

        (new ResetPasswordRequestModel())->update($resetRequest['id_request'], [
            'status'        => 'Ditolak',
            'diproses_oleh' => session()->get('username'),
            'processed_at'  => date('Y-m-d H:i:s'),
        ]);

        (new LogAktivitasModel())->catat('Tolak Reset Password', 'Akun Karyawan', "Menolak permintaan reset password akun karyawan {$akun['username']}.");

        return redirect()->to('/akun-karyawan')->with('success', 'Permintaan reset password ditolak.');
    }

    /**
     * @return array{0: array|null, 1: array|null, 2: string|null} [resetRequest, akun, pesanError]
     */
    private function ambilPermintaanReset($idRequest): array
    {
        $resetRequest = (new ResetPasswordRequestModel())->find($idRequest);
        if (! $resetRequest || $resetRequest['status'] !== 'Pending') {
            return [null, null, 'Permintaan tidak ditemukan atau sudah diproses.'];
        }

        $akun = $this->model->find($resetRequest['id_user']);
        if (! $akun || $akun['role'] !== 'Karyawan') {
            return [null, null, 'Data tidak ditemukan.'];
        }

        return [$resetRequest, $akun, null];
    }
}
