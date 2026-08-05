<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-wallet-fill"></i></div>
        <div>
            <div class="page-header-title">Gaji Saya</div>
            <p class="page-header-sub">Riwayat slip gaji Anda yang sudah diproses.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode Slip</th>
                        <th>Periode</th>
                        <th>Total Gaji</th>
                        <th>Tanggal Dibayar</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($row['kode_slip']) ?></td>
                        <td><?= esc($row['bulan']) ?>/<?= esc($row['tahun']) ?></td>
                        <td>Rp <?= number_format((float) $row['total_gaji'], 0, ',', '.') ?></td>
                        <td><?= esc($row['tanggal_gaji']) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('gaji-saya/cetak/' . $row['id_penggajian']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">Cetak Slip</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="5"><div class="empty-state"><i class="bi bi-wallet2"></i>Belum ada slip gaji untuk Anda.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
