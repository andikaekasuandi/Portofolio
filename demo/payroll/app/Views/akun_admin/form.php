<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-person-badge-fill"></i></div>
        <div>
            <div class="page-header-title"><?= $akun ? 'Edit Akun Admin' : 'Tambah Akun Admin' ?></div>
            <p class="page-header-sub">Lengkapi informasi akun login Admin di bawah ini.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= $akun ? site_url('akun-admin/update/' . $akun['id_user']) : site_url('akun-admin/store') ?>">
            <?= csrf_field() ?>

            <?php if (! $akun): ?>
            <div class="mb-3">
                <label class="form-label">Karyawan</label>
                <select name="id_karyawan" id="id_karyawan" class="form-select" required>
                    <option value="">-- Pilih nama karyawan --</option>
                    <?php foreach ($karyawan as $k): ?>
                    <option value="<?= esc($k['id_karyawan']) ?>" data-kode="<?= esc($k['kode_karyawan']) ?>">
                        <?= esc($k['nama']) ?> (<?= esc($k['kode_karyawan']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Akun admin baru akan dibuat untuk karyawan yang dipilih. Ini tidak mengubah akun login karyawan yang bersangkutan.</div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= esc($akun['username'] ?? old('username')) ?>" required maxlength="50">
            </div>
            <div class="mb-3">
                <label class="form-label">Password <?= $akun ? '(kosongkan jika tidak ingin mengubah)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $akun ? '' : 'required' ?>>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('akun-admin') ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>

<?php if (! $akun): ?>
<script>
    document.getElementById('id_karyawan').addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        var username = document.getElementById('username');
        if (opt.dataset.kode && !username.value) {
            username.value = opt.dataset.kode + '-admin';
        }
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>
