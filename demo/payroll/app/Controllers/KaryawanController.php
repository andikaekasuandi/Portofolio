<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\JabatanModel;
use App\Models\KaryawanModel;
use App\Models\LogAktivitasModel;
use App\Models\UserModel;

class KaryawanController extends BaseController
{
    protected KaryawanModel $model;

    public function __construct()
    {
        $this->model = new KaryawanModel();
    }

    public function index()
    {
        return view('karyawan/index', ['data' => $this->model->getWithJabatan()]);
    }

    public function create()
    {
        $jabatanModel = new JabatanModel();

        return view('karyawan/form', [
            'karyawan'       => null,
            'jabatan'        => $jabatanModel->findAll(),
            'kodeBerikutnya' => $this->model->generateKodeKaryawan(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost([
            'nama', 'jenis_kelamin', 'no_hp', 'alamat',
            'tanggal_masuk', 'status', 'id_jabatan', 'nama_bank', 'nomor_rekening',
        ]);
        $data['kode_karyawan'] = $this->model->generateKodeKaryawan();

        $roleBaru = $this->rolePerJabatan((int) $data['id_jabatan']);

        if ($roleBaru === 'Admin' && session()->get('role') !== 'Owner') {
            return redirect()->back()->withInput()->with('error', 'Hanya Owner yang bisa menjadikan Administrator.');
        }

        $userData = null;
        if ($roleBaru !== null) {
            $userData = [
                'username'   => $this->request->getPost('username'),
                'password'   => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'       => $roleBaru,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $userModel = new UserModel();
            if (! $userModel->validate($userData + ['id_karyawan' => null])) {
                return redirect()->back()->withInput()->with('errors', $userModel->errors());
            }
        }

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        $idKaryawan = $this->model->getInsertID();

        (new AbsensiModel())->generateDummySejakMasuk($idKaryawan, $data['tanggal_masuk']);

        (new LogAktivitasModel())->catat('Tambah', 'Karyawan', "Menambahkan karyawan {$data['nama']} ({$data['kode_karyawan']}).");

        if ($roleBaru !== null) {
            $userData['id_karyawan'] = $idKaryawan;
            (new UserModel())->save($userData);

            (new LogAktivitasModel())->catat(
                'Tambah',
                'Akun ' . $roleBaru,
                "Membuat akun {$roleBaru} {$userData['username']} untuk karyawan {$data['nama']} ({$data['kode_karyawan']})."
            );
        }

        return redirect()->to('/karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jabatanModel = new JabatanModel();
        $karyawan     = $this->model->find($id);

        if (! $karyawan) {
            return redirect()->to('/karyawan')->with('error', 'Data tidak ditemukan.');
        }

        return view('karyawan/form', [
            'karyawan' => $karyawan,
            'jabatan'  => $jabatanModel->findAll(),
        ]);
    }

    public function update($id)
    {
        $karyawan = $this->model->find($id);
        if (! $karyawan) {
            return redirect()->to('/karyawan')->with('error', 'Data tidak ditemukan.');
        }

        $data = $this->request->getPost([
            'nama', 'jenis_kelamin', 'no_hp', 'alamat',
            'tanggal_masuk', 'status', 'id_jabatan', 'nama_bank', 'nomor_rekening',
        ]);
        $data['id_karyawan']   = $id;
        $data['kode_karyawan'] = $karyawan['kode_karyawan'];

        $roleLama = $this->rolePerJabatan((int) $karyawan['id_jabatan']);
        $roleBaru = $this->rolePerJabatan((int) $data['id_jabatan']);
        $promosi  = $roleBaru !== null && $roleBaru !== $roleLama;
        $demosi   = $roleLama !== null && $roleBaru !== $roleLama;

        // Kalau Administrator terlibat (baik dari role lama maupun role baru), wajib Owner
        $melibatkanAdmin = $roleLama === 'Admin' || $roleBaru === 'Admin';
        if (($promosi || $demosi) && $melibatkanAdmin && session()->get('role') !== 'Owner') {
            return redirect()->back()->withInput()->with('error', 'Hanya Owner yang bisa mengubah jabatan Administrator.');
        }

        $userData = null;
        if ($promosi) {
            $userModel = new UserModel();

            if (in_array((int) $id, $userModel->getKaryawanIdsWithAccount(), true)) {
                return redirect()->back()->withInput()->with('error', 'Karyawan ini sudah memiliki akun login. Kelola lewat menu Kelola Akun Admin/Karyawan.');
            }

            $userData = [
                'username'    => $this->request->getPost('username'),
                'password'    => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'        => $roleBaru,
                'id_karyawan' => (int) $id,
                'created_at'  => date('Y-m-d H:i:s'),
            ];

            if (! $userModel->validate($userData)) {
                return redirect()->back()->withInput()->with('errors', $userModel->errors());
            }
        }

        if (! $this->model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        (new LogAktivitasModel())->catat('Ubah', 'Karyawan', "Mengubah data karyawan {$data['nama']} ({$data['kode_karyawan']}).");

        if ($promosi) {
            (new UserModel())->save($userData);

            (new LogAktivitasModel())->catat(
                'Tambah',
                'Akun ' . $roleBaru,
                "Membuat akun {$roleBaru} {$userData['username']} untuk karyawan {$data['nama']} ({$data['kode_karyawan']})."
            );
        }

        if ($demosi) {
            $akunLama = (new UserModel())->where('id_karyawan', $id)->where('role', $roleLama)->first();
            if ($akunLama) {
                (new UserModel())->delete($akunLama['id_user']);

                (new LogAktivitasModel())->catat(
                    'Hapus',
                    'Akun ' . $roleLama,
                    "Akun {$roleLama} {$akunLama['username']} otomatis dihapus karena jabatan {$data['nama']} diubah dari jabatan sebelumnya."
                );
            }
        }

        return redirect()->to('/karyawan')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $karyawan = $this->model->find($id);
        if (! $karyawan) {
            return redirect()->to('/karyawan')->with('error', 'Data tidak ditemukan.');
        }

        try {
            $this->model->delete($id);
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // Error 1451: masih ada data terkait (absensi, penggajian, akun) yang mereferensikan karyawan ini.
            if (str_contains($e->getMessage(), '1451') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->to('/karyawan')->with(
                    'error',
                    "Karyawan {$karyawan['nama']} tidak bisa dihapus karena masih memiliki data terkait (absensi, penggajian, atau akun login). Hapus atau pindahkan data tersebut terlebih dahulu."
                );
            }

            throw $e;
        }

        (new LogAktivitasModel())->catat('Hapus', 'Karyawan', "Menghapus karyawan {$karyawan['nama']} ({$karyawan['kode_karyawan']}).");

        return redirect()->to('/karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }

    /**
     * Menentukan role akun login yang berlaku untuk sebuah jabatan.
     * - Administrator -> role 'Admin' (hanya Owner yang boleh menetapkan)
     * - Staff/Karyawan -> role 'Karyawan'
     * - Jabatan lain (Manager, Supervisor, dst) -> tidak ada akun login (null)
     */
    private function rolePerJabatan(int $idJabatan): ?string
    {
        $jabatan = (new JabatanModel())->find($idJabatan);
        if (! $jabatan) {
            return null;
        }

        if ($jabatan['nama_jabatan'] === 'Administrator') {
            return 'Admin';
        }

        if ($jabatan['nama_jabatan'] === 'Staff/Karyawan') {
            return 'Karyawan';
        }

        return null;
    }
}