<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon"><i class="bi bi-calendar-check-fill"></i></div>
        <div>
            <div class="page-header-title"><?= $absensi ? 'Edit Absensi' : 'Catat Absensi' ?></div>
            <p class="page-header-sub">Isi data kehadiran karyawan di bawah ini.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= $absensi ? site_url('absensi/update/' . $absensi['id_absensi']) : site_url('absensi/store') ?>">
            <?= csrf_field() ?>

            <?php
                $karyawanTerpilih = null;
                if (! empty($absensi['id_karyawan'])) {
                    foreach ($karyawan as $k) {
                        if ($k['id_karyawan'] == $absensi['id_karyawan']) {
                            $karyawanTerpilih = $k;
                            break;
                        }
                    }
                }
            ?>
            <div class="mb-3 position-relative">
                <label class="form-label">Karyawan</label>
                <div class="position-relative">
                    <input type="text"
                           id="cariKaryawan"
                           class="form-control pe-4"
                           placeholder="-- Pilih Karyawan --"
                           autocomplete="off"
                           value="<?= $karyawanTerpilih ? esc($karyawanTerpilih['nama'] . ' (' . $karyawanTerpilih['kode_karyawan'] . ')') : '' ?>"
                           required>
                    <i class="bi bi-chevron-down position-absolute" style="right: 0.9rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #8a7d79; font-size: 0.8rem;"></i>
                </div>
                <input type="hidden" name="id_karyawan" id="idKaryawanTerpilih" value="<?= esc($absensi['id_karyawan'] ?? '') ?>" required>

                <div id="dropdownKaryawan"
                     class="list-group position-absolute w-100 shadow-sm"
                     style="max-height: 280px; overflow-y: auto; z-index: 1000; display: none; top: 100%;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= esc($absensi['tanggal'] ?? old('tanggal') ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <?php $st = $absensi['status'] ?? old('status'); ?>
                    <?php foreach (['Hadir', 'Izin', 'Sakit', 'Alpha'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= $st === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Jam Lembur</label>
                <input type="number" name="jam_lembur" class="form-control" value="<?= esc($absensi['jam_lembur'] ?? old('jam_lembur') ?? 0) ?>" min="0">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('absensi') ?>" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    const daftarKaryawanData = <?= json_encode(array_map(function ($k) {
        return [
            'id'   => $k['id_karyawan'],
            'nama' => $k['nama'],
            'kode' => $k['kode_karyawan'],
        ];
    }, $karyawan)) ?>;

    (function () {
        const input       = document.getElementById('cariKaryawan');
        const hiddenInput = document.getElementById('idKaryawanTerpilih');
        const dropdown    = document.getElementById('dropdownKaryawan');

        function renderList(data) {
            dropdown.innerHTML = '';

            if (data.length === 0) {
                dropdown.innerHTML = '<div class="list-group-item text-secondary">Tidak ditemukan.</div>';
                dropdown.style.display = 'block';
                return;
            }

            data.forEach(function (k) {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action';
                item.textContent = k.nama + ' (' + k.kode + ')';

                item.addEventListener('click', function () {
                    hiddenInput.value = k.id;
                    input.value = k.nama + ' (' + k.kode + ')';
                    dropdown.style.display = 'none';
                });

                dropdown.appendChild(item);
            });

            dropdown.style.display = 'block';
        }

        input.addEventListener('input', function () {
            hiddenInput.value = '';
            const q = input.value.toLowerCase().trim();

            if (q === '') {
                renderList(daftarKaryawanData);
                return;
            }

            const filtered = daftarKaryawanData.filter(function (k) {
                return k.nama.toLowerCase().includes(q) || k.kode.toLowerCase().includes(q);
            });

            renderList(filtered);
        });

        input.addEventListener('focus', function () {
            renderList(daftarKaryawanData);
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && e.target !== input) {
                dropdown.style.display = 'none';
            }
        });
    })();
</script>

<?= $this->endSection() ?>