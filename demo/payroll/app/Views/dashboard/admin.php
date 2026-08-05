<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="dashboard-hero reveal-on-load" style="animation-delay: .05s;">
    <div class="dashboard-hero-title">Selamat datang, <?= esc(session()->get('nama') ?? 'Admin') ?> </div>
    <a href="<?= site_url('penggajian') ?>" class="btn-hero"><i class="bi bi-wallet-fill"></i> Mulai Proses Gaji</a>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .15s;">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-value"><?= (int) ($total_karyawan ?? 0) ?></div>
            <div class="text-secondary small">Orang</div>
            <div class="stat-footer">
                <span>Semua karyawan aktif</span>
                <a href="<?= site_url('karyawan') ?>" class="text-decoration-none text-secondary"><i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .2s;">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div class="stat-label">Absensi Hari Ini</div>
            <div class="stat-value"><?= (int) ($absensi_hari_ini ?? 0) ?></div>
            <div class="text-secondary small">Orang</div>
            <div class="stat-footer">
                <span class="text-success">
                    <?= $total_karyawan > 0 ? number_format((($absensi_hari_ini ?? 0) / $total_karyawan) * 100, 2) : 0 ?>%
                    dari total karyawan
                </span>
                <a href="<?= site_url('absensi') ?>" class="text-decoration-none text-secondary"><i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .25s;">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-label">Penggajian Bulan Ini</div>
            <div class="stat-value fs-5">Rp <?= number_format((float) ($penggajian_bulan_ini ?? 0), 0, ',', '.') ?></div>
            <div class="text-secondary small">Total pembayaran</div>
            <div class="stat-footer">
                <span class="text-success">Total gaji dibayarkan</span>
                <a href="<?= site_url('penggajian') ?>" class="text-decoration-none text-secondary"><i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .3s;">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-briefcase-fill"></i>
            </div>
            <div class="stat-label">Total Jabatan</div>
            <div class="stat-value"><?= (int) ($total_jabatan ?? 0) ?></div>
            <div class="text-secondary small">Jabatan</div>
            <div class="stat-footer">
                <span>Struktur organisasi</span>
                <a href="<?= site_url('jabatan') ?>" class="text-decoration-none text-secondary"><i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<?= view('dashboard/_tentang_pt') ?>

<?= $this->endSection() ?>