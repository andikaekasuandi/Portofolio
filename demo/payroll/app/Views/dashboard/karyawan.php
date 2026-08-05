<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="dashboard-hero reveal-on-load" style="animation-delay: .05s;">
    <div class="dashboard-hero-eyebrow">PT KECAP &middot; Karya Ekspedisi Cepat Aman dan Profesional</div>
    <div class="dashboard-hero-title">Selamat Datang,<br><?= esc(session()->get('nama') ?? 'Karyawan') ?> </div>
    <p class="dashboard-hero-sub">Ini ringkasan absensi dan gaji Anda bulan ini. Cek riwayat lengkapnya lewat menu Absensi Saya dan Gaji Saya di samping.</p>
    <a href="<?= site_url('gaji-saya') ?>" class="btn-hero"><i class="bi bi-wallet-fill"></i> Lihat Slip Gaji</a>
</div>

<?php if (empty($karyawan)): ?>
<div class="alert alert-info">Akun Anda belum terhubung ke data karyawan. Silakan hubungi Admin.</div>
<?php else: ?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .15s;">
            <div class="stat-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-label">Hadir Bulan Ini</div>
            <div class="stat-value"><?= (int) ($rekap_absensi['Hadir'] ?? 0) ?></div>
            <div class="text-secondary small">Hari</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .2s;">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-label">Izin / Sakit</div>
            <div class="stat-value"><?= (int) (($rekap_absensi['Izin'] ?? 0) + ($rekap_absensi['Sakit'] ?? 0)) ?></div>
            <div class="text-secondary small">Hari</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .25s;">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-label">Alpha</div>
            <div class="stat-value"><?= (int) ($rekap_absensi['Alpha'] ?? 0) ?></div>
            <div class="text-secondary small">Hari</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card reveal-on-load" style="animation-delay: .3s;">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-stopwatch-fill"></i>
            </div>
            <div class="stat-label">Jam Lembur</div>
            <div class="stat-value"><?= (int) ($rekap_absensi['total_lembur'] ?? 0) ?></div>
            <div class="text-secondary small">Jam</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="panel-card reveal-on-load" style="animation-delay: .35s;">
            <div class="panel-title mb-3">Data Karyawan</div>
            <table class="table table-borderless mb-0">
                <tr><td class="text-secondary" style="width: 40%;">Kode Karyawan</td><td class="fw-semibold"><?= esc($karyawan['kode_karyawan']) ?></td></tr>
                <tr><td class="text-secondary">Nama</td><td class="fw-semibold"><?= esc($karyawan['nama']) ?></td></tr>
                <tr><td class="text-secondary">Jabatan</td><td class="fw-semibold"><?= esc($karyawan['nama_jabatan']) ?></td></tr>
                <tr><td class="text-secondary">Status</td><td class="fw-semibold"><?= esc($karyawan['status']) ?></td></tr>
                <tr><td class="text-secondary">Tanggal Masuk</td><td class="fw-semibold"><?= esc($karyawan['tanggal_masuk']) ?></td></tr>
            </table>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel-card reveal-on-load" style="animation-delay: .4s;">
            <div class="panel-title mb-3">Slip Gaji Terakhir</div>
            <?php if ($slip_terakhir): ?>
                <table class="table table-borderless mb-3">
                    <tr><td class="text-secondary" style="width: 40%;">Periode</td><td class="fw-semibold"><?= esc($slip_terakhir['bulan']) ?>/<?= esc($slip_terakhir['tahun']) ?></td></tr>
                    <tr><td class="text-secondary">Total Gaji</td><td class="fw-semibold">Rp <?= number_format((float) $slip_terakhir['total_gaji'], 0, ',', '.') ?></td></tr>
                </table>
                <a href="<?= site_url('gaji-saya/cetak/' . $slip_terakhir['id_penggajian']) ?>" class="btn btn-outline-primary btn-sm" target="_blank"><i class="bi bi-printer-fill me-1"></i>Cetak Slip</a>
            <?php else: ?>
                <div class="empty-state py-3"><i class="bi bi-wallet2"></i>Belum ada slip gaji yang diproses.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>
