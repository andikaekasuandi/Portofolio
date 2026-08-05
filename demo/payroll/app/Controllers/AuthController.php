<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Models\LogAktivitasModel;
use App\Models\ResetPasswordRequestModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        $nama = $user['username'];
        if (! empty($user['id_karyawan'])) {
            $karyawan = (new KaryawanModel())->find($user['id_karyawan']);
            if ($karyawan) {
                $nama = $karyawan['nama'];
            }
        }

        session()->set([
            'logged_in'             => true,
            'id_user'               => $user['id_user'],
            'username'              => $user['username'],
            'role'                  => $user['role'],
            'id_karyawan'           => $user['id_karyawan'],
            'nama'                  => $nama,
            'force_sidebar_collapse' => true,
        ]);

        (new LogAktivitasModel())->catat('Login', 'Auth', "Login sebagai {$user['role']}.");

        return redirect()->to('/dashboard')->with('success', 'Login berhasil, selamat datang ' . $user['username'] . '!');
    }

    public function lupaPassword()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/lupa_password');
    }

    public function kirimLupaPassword()
    {
        $peran    = $this->request->getPost('peran');
        $username = $this->request->getPost('username');
        $catatan  = $this->request->getPost('catatan');

        if (! in_array($peran, ['Admin', 'Karyawan'], true)) {
            return redirect()->back()->withInput()->with('error', 'Pilih peran Admin atau Karyawan terlebih dahulu.');
        }

        $user = (new UserModel())->findByUsername($username);

        if (! $user || $user['role'] !== $peran) {
            return redirect()->back()->withInput()->with('error', "Username tidak ditemukan atau bukan akun {$peran}. Kalau Anda login sebagai Owner, hubungi pengelola sistem secara langsung.");
        }

        $penanggungJawab = $peran === 'Admin' ? 'Owner' : 'Admin';

        $requestModel = new ResetPasswordRequestModel();
        if ($requestModel->adaPending((int) $user['id_user'])) {
            return redirect()->to('/login')->with('success', "Permintaan reset password Anda sebelumnya masih menunggu konfirmasi {$penanggungJawab}. Mohon tunggu.");
        }

        $requestModel->insert([
            'id_user'    => $user['id_user'],
            'catatan'    => $catatan ?: null,
            'status'     => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        (new LogAktivitasModel())->catat(
            'Request Lupa Password',
            $peran === 'Admin' ? 'Akun Admin' : 'Akun Karyawan',
            "{$peran} {$user['username']} mengajukan permintaan reset password.",
            ['id_user' => $user['id_user'], 'username' => $user['username'], 'role' => $user['role']]
        );

        return redirect()->to('/login')->with('success', "Permintaan reset password sudah dikirim ke {$penanggungJawab}. Mohon tunggu konfirmasi.");
    }

    public function logout()
    {
        (new LogAktivitasModel())->catat('Logout', 'Auth', 'Logout dari sistem.');

        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }
}
