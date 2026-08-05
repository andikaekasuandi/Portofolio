<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-briefcase-fill"></i></div>
        <div>
            <div class="page-header-title">Kelola Jabatan</div>
            <p class="page-header-sub">Kelola data jabatan beserta gaji pokok, tunjangan, dan tarif lembur.</p>
        </div>
    </div>
    <a href="<?= site_url('jabatan/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Jabatan</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Tarif Lembur/Jam</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($row['kode_jabatan']) ?></td>
                        <td><?= esc($row['nama_jabatan']) ?></td>
                        <td>Rp <?= number_format((float) $row['gaji_pokok'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format((float) $row['tunjangan'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format((float) $row['tarif_lembur'], 0, ',', '.') ?></td>
                        <td class="text-end">
                            <?php if ($row['nama_jabatan'] === 'Administrator' && session()->get('role') !== 'Owner'): ?>
                                <span class="text-muted small fst-italic">Khusus Owner</span>
                            <?php else: ?>
                                <a href="<?= site_url('jabatan/edit/' . $row['id_jabatan']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="<?= site_url('jabatan/delete/' . $row['id_jabatan']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus jabatan ini?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-briefcase"></i>Belum ada data jabatan.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>