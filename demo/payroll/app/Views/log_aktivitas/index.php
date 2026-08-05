<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-clock-history"></i></div>
        <div>
            <div class="page-header-title">Log Aktivitas</div>
            <p class="page-header-sub">Riwayat login dan aktivitas yang dilakukan Owner/Admin di sistem.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= esc($row['created_at']) ?></td>
                        <td><?= esc($row['username'] ?? '-') ?></td>
                        <td><?= esc($row['role'] ?? '-') ?></td>
                        <td><?= esc($row['aksi']) ?></td>
                        <td><?= esc($row['modul']) ?></td>
                        <td><?= esc($row['keterangan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-clock-history"></i>Belum ada aktivitas tercatat.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
