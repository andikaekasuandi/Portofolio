<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-person-badge-fill"></i></div>
        <div>
            <div class="page-header-title">Kelola Akun Admin</div>
            <p class="page-header-sub">Kelola akun login dengan peran Admin.</p>
        </div>
    </div>
    <a href="<?= site_url('akun-admin/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Admin</a>
</div>

<?php if (! empty($resetPasswordRequests)): ?>
<div class="card shadow-sm border-warning mb-4">
    <div class="card-body">
        <h5 class="card-title"><i class="bi bi-key-fill me-1"></i>Permintaan Lupa Password (<?= count($resetPasswordRequests) ?>)</h5>
        <p class="text-secondary">Admin di bawah ini mengajukan permintaan reset password lewat halaman login.</p>

        <ul class="list-group">
            <?php foreach ($resetPasswordRequests as $r): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="fw-semibold"><?= esc($r['username']) ?></div>
                    <div class="text-secondary small">
                        Diajukan <?= esc(date('d/m/Y H:i', strtotime($r['created_at']))) ?>
                        <?php if (! empty($r['catatan'])): ?>
                            &middot; "<?= esc($r['catatan']) ?>"
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <a href="<?= site_url('akun-admin/reset-password/' . $r['id_request']) ?>" class="btn btn-sm btn-primary">Set Password Baru</a>
                    <a href="<?= site_url('akun-admin/reset-password/' . $r['id_request'] . '/tolak') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak permintaan reset password dari <?= esc($r['username']) ?>?')">Tolak</a>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Karyawan</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= esc($row['username']) ?></td>
                    <td><?= esc($row['nama_karyawan'] ?? '-') ?></td>
                    <td><?= esc($row['created_at']) ?></td>
                    <td>
                        <a href="<?= site_url('akun-admin/edit/' . $row['id_user']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="<?= site_url('akun-admin/delete/' . $row['id_user']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus akun ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="4"><div class="empty-state"><i class="bi bi-person-badge"></i>Belum ada akun admin.</div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
