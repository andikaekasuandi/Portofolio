<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-wallet-fill"></i></div>
        <div>
            <div class="page-header-title">Data Penggajian</div>
            <p class="page-header-sub">Riwayat slip gaji yang sudah diproses.</p>
        </div>
    </div>
    <div class="d-flex flex-column align-items-end gap-2">
        <a href="<?= site_url('penggajian/proses') ?>" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Proses Gaji Baru</a>
        <?php if (session()->get('role') === 'Owner'): ?>
        <form method="get" class="d-flex align-items-center gap-2">
            <input type="hidden" name="bulan" value="<?= esc($bulan) ?>">
            <input type="hidden" name="tahun" value="<?= esc($tahun) ?>">
            <select name="tipe" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
                <option value="" <?= $tipe === '' || $tipe === null ? 'selected' : '' ?>>Semua Tipe</option>
                <option value="karyawan" <?= $tipe === 'karyawan' ? 'selected' : '' ?>>Karyawan Biasa</option>
                <option value="admin" <?= $tipe === 'admin' ? 'selected' : '' ?>>Karyawan Admin</option>
            </select>
        </form>
        <?php endif; ?>
    </div>
</div>

<form method="get" class="row g-2 mb-3 align-items-center">
    <div class="col-auto">
        <select name="bulan" class="form-select">
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= $m == $bulan ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <input type="number" name="tahun" class="form-control" value="<?= esc($tahun) ?>" style="width: 100px;">
    </div>
    <input type="hidden" name="tipe" value="<?= esc($tipe) ?>">
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">Filter</button>
    </div>
</form>

<div class="alert alert-info">
    <?= $sudahDigaji ?> dari <?= $totalKaryawan ?> karyawan sudah digaji untuk periode <?= date('F', mktime(0, 0, 0, $bulan, 1)) ?> <?= $tahun ?>.
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode Slip</th>
                        <th>Karyawan</th>
                        <th>Periode</th>
                        <th>Total Gaji</th>
                        <th>No. Rekening</th>
                        <th>Diproses Oleh</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($row['kode_slip']) ?></td>
                        <td><?= esc($row['nama']) ?> (<?= esc($row['kode_karyawan']) ?>)</td>
                        <td><?= esc($row['bulan']) ?>/<?= esc($row['tahun']) ?></td>
                        <td>Rp <?= number_format((float) $row['total_gaji'], 0, ',', '.') ?></td>
                        <td>
                            <?php if (! empty($row['nomor_rekening'])): ?>
                                <?= esc($row['nama_bank']) ?> - <?= esc($row['nomor_rekening']) ?>
                            <?php else: ?>
                                <span class="text-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (! empty($row['diproses_oleh'])): ?>
                                <?= esc($row['diproses_oleh']) ?> <span class="text-secondary">(<?= esc($row['diproses_oleh_role']) ?>)</span>
                            <?php else: ?>
                                <span class="text-secondary">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('penggajian/cetak/' . $row['id_penggajian']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">Cetak Slip</a>
                            <?php if (session()->get('role') === 'Owner'): ?>
                            <a href="<?= site_url('penggajian/delete/' . $row['id_penggajian']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-wallet2"></i>Belum ada data penggajian untuk periode ini.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>