<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-calendar-check-fill"></i></div>
        <div>
            <div class="page-header-title">Absensi Saya</div>
            <p class="page-header-sub">Riwayat kehadiran Anda per periode.</p>
        </div>
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
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">Filter</button>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Jam Lembur</th>
                    </tr>
                </thead>
                <?php $badgeAbsensi = ['Hadir' => 'badge-soft-green', 'Izin' => 'badge-soft-blue', 'Sakit' => 'badge-soft-amber', 'Alpha' => 'badge-soft-red']; ?>
                <tbody>
                    <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?= esc($row['tanggal']) ?></td>
                        <td><span class="badge-soft <?= $badgeAbsensi[$row['status']] ?? 'badge-soft-gray' ?>"><?= esc($row['status']) ?></span></td>
                        <td><?= (int) $row['jam_lembur'] ?> jam</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($data)): ?>
                    <tr><td colspan="3"><div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada data absensi periode ini.</div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
