<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="dashboard-hero reveal-on-load" style="animation-delay: .05s;">
    <div class="dashboard-hero-title">Selamat datang, <?= esc(session()->get('nama') ?? 'Owner') ?> </div>
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
        <div class="stat-card reveal-on-load" style="animation-delay: .22s;">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-label">Gaji Diproses Bulan Ini</div>
            <div class="stat-value"><?= (int) ($total_penggajian_bulan_ini ?? 0) ?></div>
            <div class="text-secondary small">Slip gaji</div>
            <div class="stat-footer">
                <span>Total slip terbit bulan ini</span>
                <a href="<?= site_url('laporan/penggajian') ?>" class="text-decoration-none text-secondary"><i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<?= view('dashboard/_tentang_pt') ?>

<?= $this->endSection() ?>