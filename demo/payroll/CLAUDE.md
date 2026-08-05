# CLAUDE.md

File ini berisi panduan untuk Claude Code (claude.ai/code) saat mengerjakan kode di repository ini.

## Gambaran Proyek

Ini adalah aplikasi CodeIgniter 4: **Sistem Informasi Penggajian PT Kecap** (sistem informasi
penggajian / HR). Folder `system/` adalah framework CI4 bawaan (vendor) — seluruh kode aplikasi
ada di dalam `app/`. Lihat [README_INSTALL.md](README_INSTALL.md) untuk spesifikasi fungsional
awal (role, fitur, rumus gaji) yang menjadi dasar pembuatan aplikasi ini.

Entitas domain: `Jabatan` (posisi/jenjang kerja), `Karyawan` (pegawai — data HR/payroll saja,
**tidak** punya akun login), `Absensi` (kehadiran harian), `Penggajian` (data gaji/slip gaji yang
sudah diproses), `Users` (akun login, satu per Owner/Admin). Nama view, route, controller/model,
dan kolom database semuanya memakai **Bahasa Indonesia** — pertahankan konsistensi ini untuk kode
baru (jangan pakai penamaan Bahasa Inggris untuk konsep yang sama).

## Perintah (Commands)

```bash
php spark serve                  # jalankan dev server -> http://localhost:8080
php spark migrate                # jalankan migration (app/Database/Migrations)
php spark db:seed UserSeeder     # seed akun default Owner/Admin
composer test                    # jalankan seluruh test suite PHPUnit
vendor/bin/phpunit                     # sama dengan composer test
vendor/bin/phpunit --filter testName   # jalankan satu test berdasarkan nama method/class
vendor/bin/phpunit tests/path/to/FileTest.php  # jalankan satu file test
```

Database: MySQL via `mysqli`, dikonfigurasi di `.env` (`database.default.*`, nama database
`penggajian_db`). `app.baseURL` adalah `http://localhost:8080/`.

Belum ada perintah lint/format khusus proyek ini (tidak ada `.php-cs-fixer.dist.php` di root repo)
walaupun `codeigniter/coding-standard` sudah ada sebagai dev dependency — jangan mengarang
perintah lint sendiri; cukup ikuti gaya kode yang sudah ada di file yang sedang dikerjakan.

## Arsitektur

### Alur request: routes -> filter -> controller -> model -> view

- **Routes** — semua route didefinisikan di [app/Config/Routes.php](app/Config/Routes.php),
  dikelompokkan dalam blok `$routes->group()` sesuai role yang dibutuhkan. Tidak ada
  auto-discovery route; setiap endpoint didaftarkan secara eksplisit.
- **Filter auth/role** — [app/Filters/AuthFilter.php](app/Filters/AuthFilter.php), didaftarkan
  dengan alias `auth` di [app/Config/Filters.php](app/Config/Filters.php). Dipakai sebagai
  `'filter' => 'auth'` (untuk siapa saja yang sudah login) atau `'filter' => 'auth:Admin'` /
  `'auth:Owner'` / `'auth:Admin,Owner'` (dibatasi role tertentu). Filter ini mengecek flag
  session `logged_in`, dan jika ada argumen, mengecek juga nilai session `role` — redirect ke
  `/login` atau `/dashboard` jika gagal. Hanya ada dua role yang bisa login (`Owner`, `Admin`),
  jadi tidak ada kebutuhan pengecekan kepemilikan data per-baris berbasis role di controller.
- **Controllers** — meng-extend `BaseController` (`app/Controllers/BaseController.php`, saat ini
  masih tipis dan hampir kosong — jangan tambahkan bootstrapping yang tidak relevan ke sana tanpa
  alasan jelas). Setiap controller membuat instance model utamanya di constructor
  (`$this->model = new XModel()`) dan memanggil model tambahan langsung di dalam method
  masing-masing (tidak memakai service container / dependency injection). Nama method CRUD yang
  konsisten: `index`, `create`, `store`, `edit`, `update`, `delete`. Kegagalan validasi
  redirect back dengan `->withInput()->with('errors', ...)`; pesan sukses/gagal memakai flash
  data dengan key `success` / `error` (dibaca oleh layout).
- **Models** — meng-extend `CodeIgniter\Model`, `returnType = 'array'` (bukan entity), timestamp
  dimatikan (`useTimestamps = false`); kolom tanggal pada tabel diisi manual bila diperlukan.
  Validasi dideklarasikan lewat `$validationRules` dan otomatis dijalankan oleh `save()`. Query
  join untuk kebutuhan list/laporan ditaruh di method model khusus (misalnya
  `KaryawanModel::getWithJabatan()`, `PenggajianModel::getWithKaryawan()`,
  `AbsensiModel::rekapBulanan()`) — bukan di controller. Pertahankan pola ini: logika query
  antar-tabel di model, bukan di controller.
- **Views** — template PHP biasa yang dirender lewat helper `view()` bawaan CI4, tanpa
  Twig/Blade. Halaman yang butuh login meng-extend layout bersama
  [app/Views/layout/main.php](app/Views/layout/main.php) (`$this->extend('layout/main')` /
  `renderSection('content')`), yang memuat Bootstrap 5 + Bootstrap Icons dari CDN serta
  mendefinisikan sidebar/topbar dan tema warna merah marun "PT KECAP" langsung di dalam file
  tersebut (inline style). Selalu escape output dengan `esc()` untuk data apa pun yang berasal
  dari input user/database. Menu sidebar yang cuma boleh dilihat role tertentu dibungkus
  kondisional `<?php if (session()->get('role') === 'Owner'): ?>` langsung di `main.php` (lihat
  link "Kelola Akun Admin") — bukan disembunyikan lewat CSS. Aset statis kustom (logo dsb.)
  ditaruh di `public/assets/img/` dan diakses dengan `base_url('assets/img/...')`; CSS/JS pihak
  ketiga tetap memakai CDN seperti sebelumnya.

### Model Auth & Session

Tidak memakai CI4 Shield / package auth — proses login ditulis manual di `AuthController`
terhadap tabel `users` (`password_hash()` / `password_verify()`), lalu menyimpan `logged_in`,
`id_user`, `username`, `role`, `id_karyawan` langsung di session CI. `role` bernilai salah satu
dari `Owner`, `Admin` (string, dicek dengan `in_array` mode strict). Data `Karyawan` (pegawai)
murni data HR/payroll — **tidak** ada akun login yang otomatis dibuat untuknya; menambah data
karyawan lewat `KaryawanController::store()` tidak menyentuh tabel `users` sama sekali.

Owner (dan hanya Owner — route group `auth:Owner` di `Routes.php`) bisa membuat akun `Admin`
baru lewat menu **Kelola Akun Admin** (`AkunAdminController`). Form tambah akun menampilkan
dropdown nama karyawan (dari `KaryawanModel`) semata-mata sebagai sumber pilihan nama untuk
referensi tampilan (dipakai `UserModel::getAdminsWithKaryawan()` untuk join nama karyawan di
listing) — kolom `id_karyawan` di tabel `users` tidak berarti "karyawan ini dinaikkan jadi
admin", karena karyawan memang tidak punya akun login untuk dinaikkan. Karena `username` unik
untuk seluruh tabel, default username akun admin baru adalah `{kode_karyawan}-admin` (tetap bisa
diedit manual di form). `AkunAdminController::edit()` dan `::delete()` sama-sama memvalidasi
`role === 'Admin'` sebelum memproses ID dari URL, supaya akun `Owner` tidak bisa ke-edit/ke-hapus
lewat endpoint ini — pertahankan guard ini kalau menambah endpoint baru yang menerima ID akun dari
request.

Karyawan yang juga punya akun `Admin` (id-nya ada di `UserModel::getAdminKaryawanIds()`) hanya
boleh diproses gajinya oleh Owner — `PenggajianController::proses()` menyembunyikan karyawan
tersebut dari dropdown kalau yang login Admin, dan `::simpanProses()` menolak submit-nya kalau
tetap dipaksa lewat request manual. Aksi lain di modul Penggajian (cetak, hapus) untuk karyawan
yang sama **tidak** dibatasi — cuma pembuatan slip baru yang dikunci ke Owner.

### Perhitungan Gaji

Aturan bisnis inti, diimplementasikan di `PenggajianController::simpanProses()` dan
didokumentasikan di [README_INSTALL.md](README_INSTALL.md):

```
Uang Lembur = total jam lembur bulan itu x tarif_lembur (dari jabatan)
Potongan    = (jumlah hari Alpha x 2% x gaji_pokok) + (jumlah hari Izin x 1% x gaji_pokok)
Total Gaji  = gaji_pokok + tunjangan + Uang Lembur - Potongan
```

`AbsensiModel::rekapBulanan()` menghitung rekap absensi bulanan (jumlah per status + total jam
lembur) yang menjadi input rumus di atas. Satu proses penggajian dijaga agar tidak duplikat per
kombinasi pegawai/bulan/tahun lewat `PenggajianModel::sudahDiproses()`, dan kode slip gaji
dibuat berurutan per periode lewat `PenggajianModel::generateKodeSlip()` (format
`SLP{tahun}{bulan}{NNNN}`). Jika rumus ini diubah, update di controller (bukan di model) dan
pastikan deskripsi di README_INSTALL.md tetap sinkron.

## Testing

`tests/` saat ini hanya berisi test bawaan/contoh dari framework
(`tests/unit/HealthTest.php`, `tests/database/ExampleDatabaseTest.php`,
`tests/session/ExampleSessionTest.php`) — belum ada test untuk controller/model aplikasi
penggajian ini. Test database butuh env var `database.tests.*` dikonfigurasi dulu (lihat blok
comment di [phpunit.dist.xml](phpunit.dist.xml)) sebelum bisa jalan terhadap database sungguhan.
