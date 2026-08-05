<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-key-fill"></i></div>
        <div>
            <div class="page-header-title">Reset Password Admin</div>
            <p class="page-header-sub">Atur password baru untuk akun <strong><?= esc($akun['username']) ?></strong>.</p>
        </div>
    </div>
</div>

<?php if (! empty($resetRequest['catatan'])): ?>
<div class="alert alert-info">
    <strong>Catatan dari <?= esc($akun['username']) ?>:</strong> <?= esc($resetRequest['catatan']) ?>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= site_url('akun-admin/reset-password/' . $resetRequest['id_request']) ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" value="<?= esc($akun['username']) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <div class="input-group">
                    <input type="text" name="password" id="passwordBaru" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                    <button type="button" class="btn btn-outline-secondary" id="btnGenerate">Generate</button>
                </div>
                <div class="form-text">Sampaikan password ini ke Admin yang bersangkutan secara langsung (WA/lisan) setelah disimpan.</div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
            <a href="<?= site_url('akun-admin') ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    document.getElementById('btnGenerate').addEventListener('click', function () {
        var karakter = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        var hasil = '';
        for (var i = 0; i < 10; i++) {
            hasil += karakter.charAt(Math.floor(Math.random() * karakter.length));
        }
        document.getElementById('passwordBaru').value = hasil;
    });
</script>

<?= $this->endSection() ?>
