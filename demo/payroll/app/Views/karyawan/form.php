<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="page-header-title"><?= $karyawan ? 'Edit Karyawan' : 'Tambah Karyawan' ?></div>
            <p class="page-header-sub">Lengkapi informasi karyawan di bawah ini.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= $karyawan ? site_url('karyawan/update/' . $karyawan['id_karyawan']) : site_url('karyawan/store') ?>">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode Karyawan</label>
                    <input type="text" class="form-control" value="<?= esc($karyawan['kode_karyawan'] ?? $kodeBerikutnya) ?>" readonly>
                    <div class="form-text">Kode dibuat otomatis secara berurutan.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?= esc($karyawan['nama'] ?? old('nama')) ?>" required maxlength="100">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <?php $jk = $karyawan['jenis_kelamin'] ?? old('jenis_kelamin'); ?>
                        <option value="L" <?= $jk === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $jk === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= esc($karyawan['no_hp'] ?? old('no_hp')) ?>" maxlength="20">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"><?= esc($karyawan['alamat'] ?? old('alamat')) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" value="<?= esc($karyawan['tanggal_masuk'] ?? old('tanggal_masuk')) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <?php $st = $karyawan['status'] ?? old('status'); ?>
                        <option value="Tetap" <?= $st === 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                        <option value="Kontrak" <?= $st === 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jabatan</label>
                    <?php $isOwner = session()->get('role') === 'Owner'; ?>
                    <select name="id_jabatan" id="id_jabatan" class="form-select" required data-original="<?= esc($karyawan['id_jabatan'] ?? '') ?>">
                        <option value="">-- Pilih Jabatan --</option>
                        <?php foreach ($jabatan as $j): ?>
                        <?php
                            $roleUntukJabatan = '';
                            if ($j['nama_jabatan'] === 'Administrator') {
                                $roleUntukJabatan = 'Admin';
                            } elseif ($j['nama_jabatan'] === 'Staff/Karyawan') {
                                $roleUntukJabatan = 'Karyawan';
                            }
                        ?>
                        <option value="<?= $j['id_jabatan'] ?>"
                            data-nama="<?= esc($j['nama_jabatan']) ?>"
                            data-role="<?= esc($roleUntukJabatan) ?>"
                            <?= ($j['nama_jabatan'] === 'Administrator' && ! $isOwner) ? 'disabled' : '' ?>
                            <?= (isset($karyawan['id_jabatan']) && $karyawan['id_jabatan'] == $j['id_jabatan']) ? 'selected' : '' ?>>
                            <?= esc($j['nama_jabatan']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (! $isOwner): ?>
                    <div class="form-text text-danger">Hanya Owner yang bisa menjadikan Administrator.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Bank</label>
                    <input type="text" name="nama_bank" class="form-control" value="<?= esc($karyawan['nama_bank'] ?? old('nama_bank')) ?>" maxlength="50">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor Rekening</label>
                    <input type="text" name="nomor_rekening" class="form-control" value="<?= esc($karyawan['nomor_rekening'] ?? old('nomor_rekening')) ?>" maxlength="30">
                </div>
            </div>

            <div id="akun_login_baru" class="card bg-light border mb-3 d-none">
                <div class="card-body">
                    <h6 class="card-title mb-1" id="akun_login_baru_title">Akun Login</h6>
                    <p class="text-muted small mb-3" id="akun_login_baru_desc">Dibuat sekaligus supaya karyawan ini bisa login.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" maxlength="50" value="<?= esc(old('username')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div id="akun_login_sudah_ada" class="alert alert-info d-none">
                Karyawan ini sudah memiliki akun <span id="akun_sudah_ada_role"></span>. Kelola username/password lewat menu
                <a href="#" id="akun_sudah_ada_link">Kelola Akun</a>.
            </div>

            <div id="akun_login_akan_dihapus" class="alert alert-warning d-none">
                Mengubah jabatan ini akan menghapus akun <span id="akun_akan_dihapus_role"></span> milik karyawan tersebut secara otomatis.
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('karyawan') ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
(function () {
    var select           = document.getElementById('id_jabatan');
    var akunBaru         = document.getElementById('akun_login_baru');
    var akunBaruTitle    = document.getElementById('akun_login_baru_title');
    var akunBaruDesc     = document.getElementById('akun_login_baru_desc');
    var akunSudahAda     = document.getElementById('akun_login_sudah_ada');
    var akunSudahAdaRole = document.getElementById('akun_sudah_ada_role');
    var akunSudahAdaLink = document.getElementById('akun_sudah_ada_link');
    var akunAkanDihapus     = document.getElementById('akun_login_akan_dihapus');
    var akunAkanDihapusRole = document.getElementById('akun_akan_dihapus_role');
    var username         = document.getElementById('username');
    var password         = document.getElementById('password');

    var originalValue  = select.dataset.original || '';
    var originalOption = originalValue ? select.querySelector('option[value="' + originalValue + '"]') : null;
    var originalRole   = originalOption ? (originalOption.dataset.role || '') : '';

    var linkAkun = { Admin: '<?= site_url('akun-admin') ?>', Karyawan: '<?= site_url('kelola-akun-karyawan') ?>' };

    function toggle() {
        var selected = select.options[select.selectedIndex];
        var role     = selected ? (selected.dataset.role || '') : '';

        akunBaru.classList.add('d-none');
        akunSudahAda.classList.add('d-none');
        akunAkanDihapus.classList.add('d-none');
        username.required = false;
        password.required = false;

        if (role !== '' && role === originalRole) {
            akunSudahAdaRole.textContent = role;
            akunSudahAdaLink.href = linkAkun[role] || '#';
            akunSudahAdaLink.textContent = role === 'Admin' ? 'Kelola Akun Admin' : 'Kelola Akun Karyawan';
            akunSudahAda.classList.remove('d-none');
        } else if (role !== '') {
            akunBaruTitle.textContent = 'Akun Login ' + role;
            akunBaruDesc.textContent = 'Dibuat sekaligus supaya karyawan ini bisa login sebagai ' + role + '.';
            akunBaru.classList.remove('d-none');
            username.required = true;
            password.required = true;
        } else if (role === '' && originalRole !== '') {
            akunAkanDihapusRole.textContent = originalRole;
            akunAkanDihapus.classList.remove('d-none');
        }
    }

    select.addEventListener('change', toggle);
    toggle();
})();
</script>

<?= $this->endSection() ?>