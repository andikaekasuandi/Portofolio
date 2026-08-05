<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        <div>
            <div class="page-header-title">Laporan Penggajian</div>
            <p class="page-header-sub">Rekap seluruh slip gaji yang sudah diproses.</p>
        </div>
    </div>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-auto">
        <select name="bulan" class="form-select">
            <option value="">Semua Bulan</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m == $bulan ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="<?= esc($tahun) ?>" style="width: 120px;">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">Filter</button>
    </div>
</form>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">Total Keseluruhan</div>
            <div class="stat-value fs-4">Rp <?= number_format((float) $totalGaji, 0, ',', '.') ?></div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-graph-up"></i></div>
            <?php if ($rataRataBulan !== null): ?>
                <div class="stat-label">Rata-rata Gaji &middot; <?= esc(date('F', mktime(0, 0, 0, (int) $bulan, 1))) ?> <?= esc($tahun) ?></div>
                <div class="stat-value fs-4">Rp <?= number_format((float) $rataRataBulan, 0, ',', '.') ?></div>
            <?php else: ?>
                <div class="stat-label">Rata-rata Gaji per Bulan</div>
                <div class="stat-value fs-6 text-secondary">Pilih bulan buat lihat rata-ratanya</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-bar-chart-line-fill"></i></div>
            <div class="stat-label">Rata-rata Gaji &middot; Tahun <?= esc($tahun) ?></div>
            <div class="stat-value fs-4">Rp <?= number_format((float) $rataRataTahun, 0, ',', '.') ?></div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Kode Slip</th>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Periode</th>
                    <th>Total Gaji</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($row['kode_slip']) ?></td>
                    <td><?= esc($row['nama']) ?> (<?= esc($row['kode_karyawan']) ?>)</td>
                    <td><?= esc($row['nama_jabatan']) ?></td>
                    <td><?= esc($row['bulan']) ?>/<?= esc($row['tahun']) ?></td>
                    <td>Rp <?= number_format((float) $row['total_gaji'], 0, ',', '.') ?></td>
                    <td class="text-end">
                        <a href="<?= site_url('penggajian/cetak/' . $row['id_penggajian']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">Cetak</a>
                        <?php if (session()->get('role') === 'Owner'): ?>
                        <a href="<?= site_url('penggajian/delete/' . $row['id_penggajian']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin mau hapus slip gaji <?= esc($row['kode_slip'], 'js') ?>? Aksi ini tidak bisa dibatalkan.')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-file-earmark-text"></i>Belum ada data.</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>