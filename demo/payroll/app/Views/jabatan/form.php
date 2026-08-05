<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-briefcase-fill"></i></div>
        <div>
            <div class="page-header-title"><?= $jabatan ? 'Edit Jabatan' : 'Tambah Jabatan' ?></div>
            <p class="page-header-sub">Lengkapi informasi jabatan di bawah ini.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= $jabatan ? site_url('jabatan/update/' . $jabatan['id_jabatan']) : site_url('jabatan/store') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Kode Jabatan</label>
                <input type="text" name="kode_jabatan" class="form-control" value="<?= esc($jabatan['kode_jabatan'] ?? old('kode_jabatan')) ?>" required maxlength="10">
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Jabatan</label>
                <input type="text" name="nama_jabatan" class="form-control" value="<?= esc($jabatan['nama_jabatan'] ?? old('nama_jabatan')) ?>" required maxlength="50">
            </div>
            <div class="mb-3">
                <label class="form-label">Gaji Pokok</label>
                <input type="number" step="0.01" name="gaji_pokok" class="form-control" value="<?= esc($jabatan['gaji_pokok'] ?? old('gaji_pokok')) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tunjangan</label>
                <input type="number" step="0.01" name="tunjangan" class="form-control" value="<?= esc($jabatan['tunjangan'] ?? old('tunjangan') ?? 0) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Tarif Lembur / Jam</label>
                <input type="number" step="0.01" name="tarif_lembur" class="form-control" value="<?= esc($jabatan['tarif_lembur'] ?? old('tarif_lembur') ?? 0) ?>">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('jabatan') ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>