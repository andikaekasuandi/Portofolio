# Penjelasan Syntax Project — Sistem Penggajian PT Kecap

Dokumen ini menjelaskan **syntax per file/bagian** yang dipakai di project CodeIgniter 4 ini,
supaya paham *kenapa* kode ditulis seperti itu, bukan cuma *apa* isinya. Urutan pembahasan
mengikuti alur request: routing → filter → controller → model → view.

---

## 1. `app/Config/Routes.php` — Routing

```php
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::doLogin');
```

- `$routes->get(path, controller::method)` — daftarkan route untuk HTTP method `GET`.
  `$routes->post(...)` sama tapi untuk form submit (`POST`). CI4 **tidak auto-discover** route
  dari nama controller — semua endpoint harus didaftarkan manual di sini.
- String `'AuthController::login'` artinya: kalau path ini diakses, jalankan method `login()`
  di class `AuthController`.

```php
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'DashboardController::index');
    ...
});
```

- `$routes->group($prefix, $options, $callback)` — mengelompokkan beberapa route supaya
  semuanya otomatis kena `$options` yang sama (di sini: filter `auth`), tanpa perlu menulis
  `'filter' => 'auth'` di tiap baris. Prefix `''` berarti tidak menambah awalan URL apa pun,
  cuma dipakai untuk grouping filter.
- `static function (...) { ... }` — closure biasa yang jadi isi grup; `static` dipakai supaya
  closure tidak membawa `$this` (praktik umum, bukan keharusan CI4).
- Nested group (`group` di dalam `group`, contoh baris 20 & 49) artinya route di dalamnya kena
  **dua filter sekaligus**: filter grup luar (`auth`) *dan* filter grup dalam
  (`auth:Admin,Owner`).

```php
'filter' => 'auth:Admin,Owner'
```

- Ini syntax **argumen filter**. Nama filter (`auth`) dan argumennya (`Admin,Owner`) dipisah
  `:`, argumen jamak dipisah koma. Argumen ini yang diterima `AuthFilter::before()` sebagai
  parameter `$arguments` (lihat bagian 2) — dipakai untuk membatasi filter itu hanya berlaku
  untuk role tertentu.

```php
$routes->get('jabatan/edit/(:num)', 'JabatanController::edit/$1');
```

- `(:num)` — *placeholder* bawaan CI4, cuma cocok kalau segmen URL itu angka (mis. `/jabatan/edit/5`).
- `$1` — merujuk ke placeholder pertama yang match, dioper sebagai argumen pertama ke method
  `edit($id)` di controller.

---

## 2. `app/Filters/AuthFilter.php` — Filter Auth & Role

```php
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return redirect()->to('/login')->with('error', '...');
        }

        if ($arguments && ! in_array($session->get('role'), $arguments, true)) {
            return redirect()->to('/dashboard')->with('error', '...');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // tidak digunakan
    }
}
```

- `implements FilterInterface` — kontrak dari CI4: class ini **wajib** punya method `before()`
  dan `after()` supaya bisa dipakai sebagai filter route.
- `before()` dijalankan **sebelum** controller — kalau return sebuah response/redirect di sini,
  controller **tidak jadi dipanggil sama sekali** (request "dipotong" duluan).
- `$arguments` — ini persis nilai di belakang `:` pada `'filter' => 'auth:Admin,Owner'` di
  Routes.php, otomatis di-*explode* koma oleh CI4 jadi array `['Admin', 'Owner']`.
- `session()` — helper function bawaan CI4, singkatan dari `service('session')`. Bisa dipanggil
  di mana saja tanpa `use` statement.
- `$session->get('logged_in')` — ambil nilai dari session; kalau belum pernah di-`set`, akan
  bernilai `null` (falsy), jadi `! $session->get('logged_in')` = "belum login".
- `in_array($needle, $haystack, true)` — parameter ketiga `true` = **strict mode** (bandingkan
  tipe data juga, bukan cuma nilai). Penting supaya `role` string `"Admin"` tidak ketuker
  dengan nilai lain yang kebetulan "sama" secara longgar.
- `after()` sengaja dikosongkan (cuma komentar) karena project ini tidak butuh logika apa pun
  setelah controller selesai — tapi method-nya tetap wajib ada karena `FilterInterface`
  mengharuskannya.

---

## 3. `app/Controllers/BaseController.php`

```php
abstract class BaseController extends Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }
}
```

- `abstract class` — tidak bisa di-`new BaseController()` langsung, cuma boleh di-`extend`.
  Semua controller lain di project ini (`class JabatanController extends BaseController`) mewarisi
  class ini.
- `initController()` — hook bawaan CI4 yang dipanggil otomatis tiap kali controller dibuat,
  **sebelum** method aksi (`index`, `store`, dst) dipanggil. `parent::initController(...)`
  wajib dipanggil supaya inisialisasi bawaan framework (request, response, logger) tetap jalan.
  Di project ini isinya masih kosong/default — belum ada bootstrapping tambahan (sesuai
  catatan di CLAUDE.md: jangan tambah sesuatu ke sini tanpa alasan jelas).

---

## 4. Pola Controller CRUD (contoh: `JabatanController.php`)

Semua controller CRUD (`JabatanController`, `KaryawanController`, `AbsensiController`,
`PenggajianController`, `AkunAdminController`) mengikuti pola yang sama persis:

```php
class JabatanController extends BaseController
{
    protected JabatanModel $model;

    public function __construct()
    {
        $this->model = new JabatanModel();
    }
    ...
}
```

- `protected JabatanModel $model;` — **typed property** (fitur PHP 7.4+): properti `$model`
  cuma boleh diisi objek bertipe `JabatanModel` (atau `null` kalau ditandai nullable). Kalau
  diisi tipe lain, PHP akan error di runtime.
- `__construct()` — constructor dipanggil otomatis tiap kali `new JabatanController()`
  dibuat oleh router. Di sini dipakai untuk langsung siapkan `$this->model`, supaya semua
  method di bawah tinggal pakai `$this->model` tanpa `new JabatanModel()` berulang-ulang.

```php
public function index()
{
    return view('jabatan/index', ['data' => $this->model->findAll()]);
}
```

- `view($path, $data)` — helper CI4 untuk merender file di `app/Views/{$path}.php`.
  Argumen kedua adalah array asosiatif yang key-nya jadi **variabel langsung** di dalam view
  (`['data' => ...]` berarti di view ada variabel `$data`).
- `$this->model->findAll()` — method bawaan `CodeIgniter\Model`, ambil semua baris dari tabel
  (`SELECT * FROM jabatan`), dikembalikan sebagai array karena `returnType = 'array'` di model.

```php
public function store()
{
    $data = $this->request->getPost(['kode_jabatan', 'nama_jabatan', 'gaji_pokok', 'tunjangan', 'tarif_lembur']);

    if (! $this->model->save($data)) {
        return redirect()->back()->withInput()->with('errors', $this->model->errors());
    }

    return redirect()->to('/jabatan')->with('success', 'Jabatan berhasil ditambahkan.');
}
```

- `$this->request` — objek request CI4, tersedia otomatis di semua controller (di-set oleh
  `initController` di `BaseController`).
- `getPost(array $keys)` — kalau argumennya array of string, CI4 mengembalikan **array
  asosiatif** berisi cuma field-field itu dari `$_POST` (whitelist), bukan seluruh input —
  jadi field yang tidak ada di list ini tidak akan ikut ke-`save()` walau ada di form.
- `$this->model->save($data)` — method bawaan `CodeIgniter\Model` yang otomatis:
  1. Menjalankan `$validationRules` yang dideklarasikan di model (lihat bagian 5).
  2. Kalau valid **dan** `$data` tidak punya primary key → `INSERT`.
  3. Kalau valid **dan** `$data` punya primary key (mis. `id_jabatan` di method `update()`) → `UPDATE`.
  4. Return `true`/`false` tergantung validasi & query berhasil atau tidak.
- `redirect()->back()->withInput()->with('errors', ...)` — method chaining:
  - `redirect()->back()` — balik ke halaman sebelumnya (form).
  - `->withInput()` — bawa lagi input lama supaya form tidak kosong, dibaca lewat `old()` di view.
  - `->with('errors', $this->model->errors())` — kirim **flashdata**: data yang cuma hidup
    untuk **satu request berikutnya** (dibaca sekali lalu otomatis hilang). `$this->model->errors()`
    mengembalikan array pesan error validasi per field.
  - `->with('success', '...')` — flashdata dengan key `success`, dibaca oleh layout
    (`app/Views/layout/main.php`) untuk ditampilkan sebagai notifikasi.

```php
public function edit($id)
{
    $jabatan = $this->model->find($id);
    if (! $jabatan) {
        return redirect()->to('/jabatan')->with('error', 'Data tidak ditemukan.');
    }

    return view('jabatan/form', ['jabatan' => $jabatan]);
}
```

- `$id` — parameter method ini otomatis diisi dari `$1` di route
  `'jabatan/edit/(:num)', 'JabatanController::edit/$1'` (lihat bagian 1).
- `$this->model->find($id)` — method bawaan Model, `SELECT * WHERE id_jabatan = $id LIMIT 1`,
  return `null` kalau tidak ketemu.

```php
public function delete($id)
{
    $this->model->delete($id);

    return redirect()->to('/jabatan')->with('success', 'Jabatan berhasil dihapus.');
}
```

- `$this->model->delete($id)` — method bawaan Model, hapus baris berdasarkan primary key.
  Tidak ada konfirmasi di sisi server — konfirmasi "Yakin hapus?" cuma di sisi browser lewat
  `onclick="return confirm(...)"` di view (lihat bagian 6). Ini pola yang dipakai konsisten
  di semua controller CRUD project ini.

---

## 5. Pola Model (contoh: `JabatanModel.php`, `KaryawanModel.php`, dst)

```php
class JabatanModel extends Model
{
    protected $table            = 'jabatan';
    protected $primaryKey       = 'id_jabatan';
    protected $allowedFields    = ['kode_jabatan', 'nama_jabatan', 'gaji_pokok', 'tunjangan', 'tarif_lembur'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
        'id_jabatan'   => 'permit_empty|numeric',
        'kode_jabatan' => 'required|max_length[10]|is_unique[jabatan.kode_jabatan,id_jabatan,{id_jabatan}]',
        'nama_jabatan' => 'required|max_length[50]',
        ...
    ];
}
```

- `extends Model` — mewarisi `CodeIgniter\Model`, dapat gratis method `find()`, `findAll()`,
  `save()`, `delete()`, `where()`, dll tanpa nulis SQL manual.
- `$table` — nama tabel yang dipakai semua query bawaan di atas.
- `$primaryKey` — dipakai `save()` untuk memutuskan INSERT vs UPDATE, dan dipakai `find($id)`.
- `$allowedFields` — **whitelist mass-assignment**: cuma key yang ada di list ini yang boleh
  ikut ke-`INSERT`/`UPDATE` lewat `save()`. Kalau `$data` punya key di luar list ini, CI4
  akan menolaknya (mass assignment protection).
- `$returnType = 'array'` — hasil query (`find`, `findAll`, dll) dikembalikan sebagai `array`,
  **bukan** object Entity. Konsekuensinya: akses data selalu pakai `$row['nama_jabatan']`,
  bukan `$row->nama_jabatan`, konsisten di seluruh controller & view project ini.
- `$useTimestamps = false` — CI4 **tidak** otomatis mengisi `created_at`/`updated_at`. Kalau
  tabel butuh kolom tanggal, harus diisi manual (lihat `created_at` yang diisi manual di
  `AkunAdminController::store()`).
- `$validationRules` — dijalankan **otomatis** oleh `save()`, tidak perlu panggil validasi
  manual di controller. Syntax aturan pakai `|` sebagai pemisah antar-rule:
  - `required` — wajib diisi.
  - `permit_empty` — boleh kosong, tapi kalau diisi tetap divalidasi rule setelahnya.
  - `max_length[10]` — argumen rule ditulis di dalam `[...]`.
  - `numeric`, `in_list[L,P]`, `valid_date` — validator bawaan CI4.
  - `is_unique[jabatan.kode_jabatan,id_jabatan,{id_jabatan}]` — format:
    `is_unique[tabel.kolom,kolom_pengecualian,{placeholder}]`. `{id_jabatan}` adalah
    **placeholder otomatis**: CI4 menggantinya dengan nilai `$data['id_jabatan']` saat
    validasi jalan — ini yang bikin validasi unique tetap lolos saat **update** data yang
    kode-nya sama dengan dirinya sendiri (kalau tidak ada exception ini, update record tanpa
    ubah kode akan selalu gagal dianggap "duplikat").

### Method query kustom (join antar tabel)

```php
public function getWithJabatan($id = null)
{
    $builder = $this->select('karyawan.*, jabatan.nama_jabatan, jabatan.gaji_pokok, jabatan.tunjangan, jabatan.tarif_lembur')
        ->join('jabatan', 'jabatan.id_jabatan = karyawan.id_jabatan');

    if ($id !== null) {
        return $builder->where('karyawan.id_karyawan', $id)->first();
    }

    return $builder->findAll();
}
```

- Ini **Query Builder** bawaan CI4 (`$this->select()->join()->where()->findAll()`), dipanggil
  langsung dari dalam Model (bukan dari controller) — sesuai pola project ini: "logika query
  antar-tabel di model, bukan di controller" (lihat CLAUDE.md).
- Method chaining: tiap method (`select`, `join`, `where`) mengembalikan builder yang sama,
  jadi bisa disambung terus sampai ditutup dengan method yang benar-benar menjalankan query
  (`findAll()`, `first()`, `get()`).
- Parameter `$id = null` dengan default value — bikin satu method bisa dipakai untuk dua
  kebutuhan: `getWithJabatan()` (semua data, tanpa argumen) dan `getWithJabatan($id)` (satu
  data spesifik). Pola ini diulang persis di `PenggajianModel::getWithKaryawan()`.
- `$this->db->table('absensi')` di `AbsensiModel::getWithKaryawan()` — cara alternatif memulai
  query builder langsung dari koneksi DB (`$this->db`), dipakai di situ karena builder-nya
  butuh nama tabel eksplisit sebelum `select()`.
- `$builder->where('MONTH(absensi.tanggal)', (int) $bulan, false)` — parameter ketiga `false`
  memberitahu CI4 untuk **tidak** meng-escape/membungkus argumen pertama dengan backtick,
  karena `MONTH(...)` adalah ekspresi SQL, bukan nama kolom biasa.

### Method dengan return type & logika non-query

```php
public function rekapBulanan(int $idKaryawan, int $bulan, int $tahun): array
{
    $rows = $this->where('id_karyawan', $idKaryawan)
        ->where('MONTH(tanggal)', $bulan)
        ->where('YEAR(tanggal)', $tahun)
        ->findAll();

    $rekap = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpha' => 0, 'total_lembur' => 0];
    foreach ($rows as $r) {
        $rekap[$r['status']]++;
        $rekap['total_lembur'] += (int) $r['jam_lembur'];
    }

    return $rekap;
}
```

- `function rekapBulanan(int $idKaryawan, int $bulan, int $tahun): array` — **type hinting**
  penuh: tipe tiap parameter dideklarasikan (`int`), dan tipe return value juga (`: array`).
  Kalau dipanggil dengan tipe yang salah atau return value bukan array, PHP melempar
  `TypeError`.
- `$rekap[$r['status']]++` — trik: karena tiap baris absensi punya `status` yang nilainya
  persis `'Hadir'`/`'Izin'`/`'Sakit'`/`'Alpha'`, key itu dipakai langsung untuk increment
  counter yang sesuai di array `$rekap` — tidak perlu `if/elseif` berjenjang per status.
- `generateKodeSlip()` (di `PenggajianModel`) pakai pola serupa: `like()` + `substr()` +
  `str_pad()` untuk menghasilkan nomor urut per periode (`SLP{tahun}{bulan}{NNNN}`) — dijelaskan
  lebih detail di CLAUDE.md bagian "Perhitungan Gaji".

---

## 6. Pola View (contoh: `jabatan/index.php` & `jabatan/form.php`)

Semua view CRUD (`jabatan`, `karyawan`, `absensi`, `penggajian`, `akun_admin`) mengikuti
struktur yang sama: satu file `index.php` (tabel data) + satu file `form.php` (dipakai
bareng untuk create **dan** edit).

```php
<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
...
<?= $this->endSection() ?>
```

- Ini **CI4 View Layout system** (bukan Twig/Blade — plain PHP). `$this->extend('layout/main')`
  bilang "pakai `app/Views/layout/main.php` sebagai kerangka luar". `section('content')` /
  `endSection()` menandai bagian mana dari file ini yang akan disisipkan ke slot `content` di
  layout tersebut (lihat `renderSection('content')` di `layout/main.php`).
- `<?= ... ?>` — short echo tag PHP, sama dengan `<?php echo ...; ?>`.

```php
<?php foreach ($data as $row): ?>
    <td class="fw-semibold"><?= esc($row['kode_jabatan']) ?></td>
<?php endforeach; ?>
```

- Sintaks **alternatif** PHP (`foreach (...): ... endforeach;`) — dipakai supaya campuran
  PHP+HTML lebih gampang dibaca daripada `foreach (...) { ... }` dengan kurung kurawal.
- `$data` — ini persis variabel yang dikirim controller lewat `view('jabatan/index', ['data' => ...])`.
- `esc($row['kode_jabatan'])` — **wajib** dipakai untuk semua data dari user/database yang
  ditampilkan ke HTML. Fungsi ini meng-escape karakter spesial (`<`, `>`, `"`, dll) supaya
  mencegah **XSS** — kalau isi data kebetulan mengandung tag HTML/script, itu akan tampil
  sebagai teks biasa, bukan dieksekusi browser.
- `number_format((float) $row['gaji_pokok'], 0, ',', '.')` — fungsi PHP native untuk format
  angka jadi gaya Indonesia (pemisah ribuan `.`, tanpa desimal): `1000000` → `1.000.000`.

```php
<form method="post" action="<?= $jabatan ? site_url('jabatan/update/' . $jabatan['id_jabatan']) : site_url('jabatan/store') ?>">
    <?= csrf_field() ?>
    <input type="text" name="kode_jabatan" value="<?= esc($jabatan['kode_jabatan'] ?? old('kode_jabatan')) ?>" required maxlength="10">
```

- `$jabatan ? ... : ...` — **ternary**: kalau variabel `$jabatan` truthy (mode edit, dikirim
  dari `edit()`) form action-nya ke `update`, kalau `null` (mode create, dikirim dari
  `create()`) action-nya ke `store`. Satu file `form.php` dipakai dua mode sekaligus.
- `csrf_field()` — helper CI4, menyisipkan `<input type="hidden">` berisi token CSRF. Wajib
  ada di semua `<form method="post">` karena CI4 secara default mengaktifkan proteksi CSRF
  global — form tanpa token ini akan ditolak.
- `$jabatan['kode_jabatan'] ?? old('kode_jabatan')` — **null coalescing operator** (`??`):
  kalau mode edit, `$jabatan['kode_jabatan']` ada isinya → dipakai. Kalau mode create,
  `$jabatan` adalah `null` sehingga `$jabatan['kode_jabatan']` juga `null` → fallback ke
  `old('kode_jabatan')`, helper CI4 yang mengambil input lama dari flashdata (diisi oleh
  `->withInput()` di controller saat validasi gagal) — supaya form tidak kosong lagi setelah
  redirect balik akibat error.
- `site_url('jabatan/store')` — helper CI4, generate URL absolut berdasarkan `app.baseURL` di
  `.env` (mis. `http://localhost:8080/jabatan/store`). Selalu pakai ini (bukan hardcode path)
  supaya link tidak rusak kalau base URL berubah.

---

## 7. Session & Flashdata — dipakai lintas file

Pola ini muncul berulang di banyak file (`AuthController`, semua controller CRUD, `layout/main.php`):

```php
session()->set([...]);          // AuthController::doLogin() — simpan data login permanen selama sesi
session()->get('role');         // dibaca di mana saja untuk cek role user saat ini
session()->destroy();           // AuthController::logout() — hapus semua data session

redirect()->to('/jabatan')->with('success', '...'); // kirim flashdata (sekali pakai)
session()->get('nama') ?? '-';  // dibaca di layout/main.php untuk tampilkan nama user login
```

- **Session** (`set`/`get`/`destroy`) — bertahan selama user login, dipakai untuk data yang
  perlu "diingat" terus: `logged_in`, `id_user`, `username`, `role`, `id_karyawan`.
- **Flashdata** (`->with(key, value)`) — cuma bertahan **satu request** setelah redirect, lalu
  otomatis hilang. Dipakai khusus untuk notifikasi sesaat: `success`, `error`, `errors` (array
  pesan validasi). Ini kenapa notifikasi sukses/gagal cuma muncul sekali lalu hilang saat
  halaman di-refresh.

---

## Ringkasan Syntax PHP Native yang Sering Dipakai

| Syntax | Contoh di project | Arti singkat |
|---|---|---|
| `??` (null coalescing) | `$jabatan['x'] ?? old('x')` | pakai kiri kalau tidak `null`, else pakai kanan |
| `? :` (ternary) | `$jabatan ? 'Edit' : 'Tambah'` | if/else satu baris |
| `(int)`, `(float)`, `(bool)` | `(int) $this->request->getPost('bulan')` | cast tipe data eksplisit |
| Typed property | `protected JabatanModel $model;` | properti class dengan tipe wajib |
| Return type | `function rekapBulanan(...): array` | tipe nilai balik method dideklarasikan |
| Alternative syntax | `foreach(...): ... endforeach;` | versi enak-dibaca untuk campuran PHP+HTML |
| Method chaining | `redirect()->back()->withInput()->with(...)` | tiap method balikin objek yang sama, disambung terus |
| Array asosiatif | `['success' => '...', 'error' => '...']` | key => value, dipakai untuk data ke view & flashdata |

---

## Catatan

Dokumen ini mengikuti pola yang **sudah konsisten** di seluruh project — kalau menambah
controller/model/view baru, ikuti pola yang sama (lihat juga panduan arsitektur lengkap di
[CLAUDE.md](CLAUDE.md)) supaya gaya kodenya tetap seragam.
