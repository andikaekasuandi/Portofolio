<?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>

    <div class="page-header">
        <div class="page-header-left">
            <div class="page-header-icon"><i class="bi bi-wallet-fill"></i></div>
            <div>
                <div class="page-header-title">Proses Penggajian</div>
                <p class="page-header-sub">Hitung dan terbitkan slip gaji untuk seluruh karyawan pada periode tertentu.</p>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        Perhitungan otomatis per karyawan: Total Gaji = Gaji Pokok + Tunjangan + (Jam Lembur x Tarif Lembur) − (2% x Gaji Pokok x Jumlah hari Alpha) − (1% x Gaji Pokok x Jumlah hari Izin). Karyawan yang gajinya sudah pernah diproses untuk periode yang sama akan dilewati otomatis. Karyawan baru cuma layak digaji begitu genap 1 bulan masa kerja dari tanggal masuknya (masuk 28 Juli &rarr; baru layak mulai 28 Agustus) &mdash; berlaku baik di proses massal maupun proses manual. Karyawan yang berstatus Admin tidak ikut di sini &mdash; gajinya diproses lewat menu Proses Gaji Karyawan Admin.
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" action="<?= site_url('penggajian/simpan-proses') ?>" id="formProsesSemua">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" max="<?= date('Y') ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Proses Gaji untuk Semua Karyawan</button>
                <a href="<?= site_url('penggajian') ?>" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-person-check-fill me-1"></i>Proses Gaji Manual (1 Karyawan)</h5>
            <p class="text-secondary">Proses gaji satu karyawan tertentu saja, di luar proses massal. Karyawan yang belum genap 1 bulan masa kerja tetap nggak bisa diproses (ditandai <span class="text-danger">*belum bisa digaji</span> di bawah) sampai tanggal kelayakannya tiba.</p>

            <?php if (empty($daftarKaryawan)): ?>
                <div class="empty-state"><i class="bi bi-people"></i>Belum ada data karyawan.</div>
            <?php else: ?>
                <form method="post" action="<?= site_url('penggajian/simpan-proses-manual') ?>" id="formProsesManual">
                    <?= csrf_field() ?>

                    <div class="col-md-4 mb-3 position-relative">
                        <label class="form-label">Karyawan</label>
                        <div class="position-relative">
                            <input type="text"
                                   id="cariKaryawan"
                                   class="form-control pe-4"
                                   placeholder="-- Pilih Karyawan --"
                                   autocomplete="off"
                                   required>
                            <i class="bi bi-chevron-down position-absolute" style="right: 0.9rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: #8a7d79; font-size: 0.8rem;"></i>
                        </div>
                        <input type="hidden" name="id_karyawan" id="idKaryawanTerpilih" required>

                        <div id="dropdownKaryawan"
                             class="list-group position-absolute w-100 shadow-sm"
                             style="max-height: 280px; overflow-y: auto; z-index: 1000; display: none; top: 100%;">
                        </div>
                    </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" max="<?= date('Y') ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Proses Gaji Karyawan Ini</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->get('role') === 'Owner'): ?>
    <div class="card shadow-sm border-warning">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-person-badge-fill me-1"></i>Proses Gaji Karyawan Admin</h5>
            <p class="text-secondary">Hanya Owner yang bisa memproses gaji untuk karyawan berikut, karena mereka juga memegang akun Admin.</p>

            <?php if (empty($adminKaryawan)): ?>
                <div class="empty-state"><i class="bi bi-person-badge"></i>Belum ada karyawan yang berstatus Admin.</div>
            <?php else: ?>
                <ul class="mb-3">
                    <?php foreach ($adminKaryawan as $k): ?>
                        <li><?= esc($k['nama']) ?> (<?= esc($k['kode_karyawan']) ?>) - <?= esc($k['nama_jabatan']) ?></li>
                    <?php endforeach; ?>
                </ul>

                <form method="post" action="<?= site_url('penggajian/simpan-proses-admin') ?>" id="formProsesAdmin">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" max="<?= date('Y') ?>" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">Proses Gaji Karyawan Admin</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Batasi pilihan bulan/tahun biar nggak bisa proses gaji buat periode yang belum lewat
        (function () {
            var TAHUN_SEKARANG = <?= (int) date('Y') ?>;
            var BULAN_SEKARANG = <?= (int) date('n') ?>;

            function batasiForm(formId) {
                var form = document.getElementById(formId);
                if (!form) return;

                var selectBulan = form.querySelector('select[name="bulan"]');
                var inputTahun  = form.querySelector('input[name="tahun"]');
                if (!selectBulan || !inputTahun) return;

                function terapkan() {
                    var tahunDipilih = parseInt(inputTahun.value, 10) || TAHUN_SEKARANG;
                    if (tahunDipilih > TAHUN_SEKARANG) {
                        tahunDipilih = TAHUN_SEKARANG;
                        inputTahun.value = TAHUN_SEKARANG;
                    }

                    Array.prototype.forEach.call(selectBulan.options, function (opt) {
                        opt.disabled = tahunDipilih >= TAHUN_SEKARANG && parseInt(opt.value, 10) > BULAN_SEKARANG;
                    });

                    var terpilih = selectBulan.options[selectBulan.selectedIndex];
                    if (terpilih && terpilih.disabled) {
                        selectBulan.value = String(BULAN_SEKARANG);
                    }
                }

                inputTahun.addEventListener('input', terapkan);
                terapkan();
            }

            batasiForm('formProsesSemua');
            batasiForm('formProsesManual');
            batasiForm('formProsesAdmin');
        })();
    </script>
    <script>
    const daftarKaryawanData = <?= json_encode(array_map(function ($k) {
        return [
            'id'             => $k['id_karyawan'],
            'nama'           => $k['nama'],
            'kode'           => $k['kode_karyawan'],
            'tanggal_masuk'  => date('d/m/Y', strtotime($k['tanggal_masuk'])),
            'eligible'       => (bool) $k['eligible'],
            'mulai_eligible' => $k['eligible'] ? null : $k['mulai_eligible'],
        ];
    }, $daftarKaryawan)) ?>;

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
                item.className = 'list-group-item list-group-item-action' + (k.eligible ? '' : ' disabled text-muted');

                let label = k.nama + ' (' + k.kode + ') · masuk ' + k.tanggal_masuk;
                if (!k.eligible) {
                    label += ' — *belum bisa digaji, mulai ' + k.mulai_eligible;
                }
                item.textContent = label;

                if (k.eligible) {
                    item.addEventListener('click', function () {
                        hiddenInput.value = k.id;
                        input.value = k.nama + ' (' + k.kode + ')';
                        dropdown.style.display = 'none';
                    });
                }

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