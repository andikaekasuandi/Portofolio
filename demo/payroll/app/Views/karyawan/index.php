<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="page-header-title">Kelola Karyawan</div>
            <p class="page-header-sub">Kelola data karyawan dan jabatannya.</p>
        </div>
    </div>
    <a href="<?= site_url('karyawan/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Karyawan</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>JK</th>
                        <th>Status</th>
                        <th>Tgl Masuk</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($row['kode_karyawan']) ?></td>
                        <td><?= esc($row['nama']) ?></td>
                        <td><?= esc($row['nama_jabatan']) ?></td>
                        <td><?= esc($row['jenis_kelamin']) ?></td>
                        <td><span class="badge-soft <?= $row['status'] === 'Tetap' ? 'badge-soft-red' : 'badge-soft-gold' ?>"><?= esc($row['status']) ?></span></td>
                        <td><?= esc($row['tanggal_masuk']) ?></td>
                        <td class="text-end">
                            <a href="<?= site_url('karyawan/edit/' . $row['id_karyawan']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="<?= site_url('karyawan/delete/' . $row['id_karyawan']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus karyawan ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i>Belum ada data karyawan.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>