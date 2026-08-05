# Sistem Informasi Penggajian PT Kecap - CodeIgniter 4

## Cara Pasang ke Project CI4 yang Sudah Ada

1. **Copy semua folder** di dalam zip ini (`app/Controllers`, `app/Models`, `app/Filters`,
   `app/Views`, `app/Database/Migrations`, `app/Database/Seeds`, `app/Config/Routes.php`)
   ke folder `app/` project CI4 kamu (`C:\xampp\htdocs\ci4\app`). Timpa file `Routes.php`
   yang lama (isi lama cuma default routes, aman ditimpa).

2. **Daftarkan AuthFilter** — buka `app/Config/Filters.php` project kamu, ikuti instruksi
   di file `Filters_SNIPPET.txt` (cukup tambah 1 baris di `$aliases`).

3. **Jalankan migration & seeder** dari terminal, di folder project:

   ```
   php spark migrate
   php spark db:seed UserSeeder
   ```

   Ini akan membuat 5 tabel (jabatan, karyawan, users, absensi, penggajian) sesuai ERD
   kamu, plus 2 akun default.

4. **Jalankan server:**

   ```
   php spark serve
   ```

   Buka `http://localhost:8080/login`

## Akun Login Default

| Role     | Username | Password    |
|----------|----------|-------------|
| Owner    | owner    | owner123    |
| Admin    | admin    | admin123    |

**PENTING:** Ganti semua password default ini setelah testing, jangan dipakai untuk production.

## Ringkasan Fitur per Role (sesuai use case diagram)

**Admin**
- Kelola Jabatan (CRUD)
- Kelola Karyawan (CRUD — data HR/payroll, tidak ada akun login yang dibuat)
- Kelola Absensi (CRUD, filter per bulan/tahun)
- Proses Penggajian (hitung otomatis dari gaji pokok + tunjangan + lembur − potongan alpha/izin)
- Cetak Slip Gaji
- Lihat Laporan Penggajian
- Lihat Dashboard

**Owner**
- Lihat Dashboard (ringkasan total karyawan & gaji diproses)
- Kelola Jabatan (CRUD)
- Kelola Karyawan (CRUD — data HR/payroll, tidak ada akun login yang dibuat)
- Kelola Absensi (CRUD, filter per bulan/tahun)
- Proses Penggajian (hitung otomatis dari gaji pokok + tunjangan + lembur − potongan alpha/izin)
- Cetak Slip Gaji
- Lihat Laporan Penggajian
- Kelola Akun Admin (CRUD)

Karyawan (pegawai) murni data HR/payroll yang dikelola Admin/Owner — tidak punya akun login
sendiri ke sistem.

## Logika Perhitungan Gaji

```
Uang Lembur   = total jam lembur bulan itu x tarif_lembur (dari jabatan)
Potongan      = (jumlah hari Alpha x 2% x gaji_pokok) + (jumlah hari Izin x 1% x gaji_pokok)
Total Gaji    = gaji_pokok + tunjangan + Uang Lembur - Potongan
```

Silakan disesuaikan lagi rumusnya (misalnya potongan BPJS, pajak, dll) di
`app/Controllers/PenggajianController.php` method `simpanProses()`.

## Struktur File yang Dibuat

```
app/Database/Migrations/   -> 5 file migration (jabatan, karyawan, users, absensi, penggajian)
app/Database/Seeds/        -> UserSeeder.php (akun default)
app/Models/                -> JabatanModel, KaryawanModel, UserModel, AbsensiModel, PenggajianModel
app/Filters/               -> AuthFilter.php (cek login + role)
app/Controllers/           -> AuthController, DashboardController, JabatanController,
                               KaryawanController, AbsensiController, PenggajianController,
                               LaporanController, AkunAdminController
app/Views/                 -> layout, auth, dashboard, jabatan, karyawan, absensi,
                               penggajian, laporan, akun_admin
app/Config/Routes.php      -> semua route dengan role-based filter
```
