<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Auth
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::doLogin');
$routes->get('logout', 'AuthController::logout');
$routes->get('lupa-password', 'AuthController::lupaPassword');
$routes->post('lupa-password', 'AuthController::kirimLupaPassword');

// Dashboard - semua role yang sudah login
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // ==== Admin & Owner ====
    $routes->group('', ['filter' => 'auth:Admin,Owner'], static function (RouteCollection $routes) {
        $routes->get('jabatan', 'JabatanController::index');
        $routes->get('jabatan/create', 'JabatanController::create');
        $routes->post('jabatan/store', 'JabatanController::store');
        $routes->get('jabatan/edit/(:num)', 'JabatanController::edit/$1');
        $routes->post('jabatan/update/(:num)', 'JabatanController::update/$1');
        $routes->get('jabatan/delete/(:num)', 'JabatanController::delete/$1');

        $routes->get('karyawan', 'KaryawanController::index');
        $routes->get('karyawan/create', 'KaryawanController::create');
        $routes->post('karyawan/store', 'KaryawanController::store');
        $routes->get('karyawan/edit/(:num)', 'KaryawanController::edit/$1');
        $routes->post('karyawan/update/(:num)', 'KaryawanController::update/$1');
        $routes->get('karyawan/delete/(:num)', 'KaryawanController::delete/$1');

        $routes->get('absensi', 'AbsensiController::index');
        $routes->get('absensi/create', 'AbsensiController::create');
        $routes->post('absensi/store', 'AbsensiController::store');
        $routes->get('absensi/edit/(:num)', 'AbsensiController::edit/$1');
        $routes->post('absensi/update/(:num)', 'AbsensiController::update/$1');
        $routes->get('absensi/delete/(:num)', 'AbsensiController::delete/$1');

        $routes->get('penggajian', 'PenggajianController::index');
        $routes->get('penggajian/proses', 'PenggajianController::proses');
        $routes->post('penggajian/simpan-proses', 'PenggajianController::simpanProses');
        $routes->post('penggajian/simpan-proses-manual', 'PenggajianController::simpanProsesManual');

        $routes->get('akun-karyawan', 'AkunKaryawanController::index');
        $routes->get('akun-karyawan/create', 'AkunKaryawanController::create');
        $routes->post('akun-karyawan/store', 'AkunKaryawanController::store');
        $routes->get('akun-karyawan/edit/(:num)', 'AkunKaryawanController::edit/$1');
        $routes->post('akun-karyawan/update/(:num)', 'AkunKaryawanController::update/$1');
        $routes->get('akun-karyawan/delete/(:num)', 'AkunKaryawanController::delete/$1');
    });

    // ==== Admin only (proses lupa password Karyawan) ====
    $routes->group('', ['filter' => 'auth:Admin'], static function (RouteCollection $routes) {
        $routes->get('akun-karyawan/reset-password/(:num)', 'AkunKaryawanController::konfirmasiResetPassword/$1');
        $routes->post('akun-karyawan/reset-password/(:num)', 'AkunKaryawanController::simpanResetPassword/$1');
        $routes->get('akun-karyawan/reset-password/(:num)/tolak', 'AkunKaryawanController::tolakResetPassword/$1');
    });

    // ==== Karyawan only (self-service) ====
    $routes->group('', ['filter' => 'auth:Karyawan'], static function (RouteCollection $routes) {
        $routes->get('absensi-saya', 'KaryawanAreaController::absensiSaya');
        $routes->get('gaji-saya', 'KaryawanAreaController::gajiSaya');
        $routes->get('gaji-saya/cetak/(:num)', 'KaryawanAreaController::cetakGajiSaya/$1');
    });

    // ==== Owner only ====
    $routes->group('', ['filter' => 'auth:Owner'], static function (RouteCollection $routes) {
        $routes->get('akun-admin', 'AkunAdminController::index');
        $routes->get('akun-admin/create', 'AkunAdminController::create');
        $routes->post('akun-admin/store', 'AkunAdminController::store');
        $routes->get('akun-admin/edit/(:num)', 'AkunAdminController::edit/$1');
        $routes->post('akun-admin/update/(:num)', 'AkunAdminController::update/$1');
        $routes->get('akun-admin/delete/(:num)', 'AkunAdminController::delete/$1');

        $routes->get('akun-admin/reset-password/(:num)', 'AkunAdminController::konfirmasiResetPassword/$1');
        $routes->post('akun-admin/reset-password/(:num)', 'AkunAdminController::simpanResetPassword/$1');
        $routes->get('akun-admin/reset-password/(:num)/tolak', 'AkunAdminController::tolakResetPassword/$1');

        $routes->get('log-aktivitas', 'LogAktivitasController::index');

        $routes->post('penggajian/simpan-proses-admin', 'PenggajianController::simpanProsesAdmin');
        $routes->get('penggajian/delete/(:num)', 'PenggajianController::delete/$1');
    });

    // ==== Admin & Owner ====
    $routes->group('', ['filter' => 'auth:Admin,Owner'], static function (RouteCollection $routes) {
        $routes->get('laporan/penggajian', 'LaporanController::penggajian');
        $routes->get('penggajian/cetak/(:num)', 'PenggajianController::cetak/$1');
    });
});