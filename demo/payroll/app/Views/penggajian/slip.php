<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?= esc($slip['kode_slip']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --brand: #c1272d;
            --ink: #1c1c1c;
            --muted: #6b6b6b;
            --rule: #cfcfcf;
        }

        * {
            font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, Arial, sans-serif;
        }

        body {
            background: #eef0ef;
            color: var(--ink);
        }

        .slip-sheet {
            max-width: 640px;
            margin: 2rem auto;
            background: #fff;
            padding: 2.25rem 2.5rem 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .slip-kode {
            text-align: right;
            font-size: 0.72rem;
            color: var(--muted);
            margin-bottom: 0.25rem;
        }

        .slip-heading {
            text-align: center;
            margin-bottom: 1.1rem;
        }

        .slip-heading .doc-title {
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            color: var(--muted);
            text-transform: uppercase;
        }

        .slip-heading .periode {
            font-size: 0.78rem;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        .slip-heading .company {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--brand);
        }

        .slip-rule {
            border: none;
            border-top: 2px solid var(--ink);
            margin: 0.75rem 0 1.1rem;
        }

        .slip-meta {
            font-size: 0.9rem;
            margin-bottom: 1.4rem;
        }

        .slip-meta div {
            display: flex;
            margin-bottom: 0.15rem;
        }

        .slip-meta .lbl {
            width: 90px;
            flex-shrink: 0;
        }

        .slip-meta .sep {
            width: 14px;
            flex-shrink: 0;
        }

        .slip-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 1.75rem;
        }

        .slip-columns h6 {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            padding-bottom: 0.4rem;
            border-bottom: 1.5px solid var(--ink);
            margin-bottom: 0.5rem;
        }

        .slip-line {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.87rem;
            padding: 0.28rem 0;
            border-bottom: 1px dotted var(--rule);
        }

        .slip-line .val {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .slip-total-line {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.87rem;
            font-weight: 700;
            padding-top: 0.6rem;
            margin-top: 0.35rem;
            border-top: 1.5px solid var(--ink);
        }

        .slip-net {
            text-align: right;
            margin-top: 1.75rem;
        }

        .slip-net .label {
            font-size: 0.75rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .slip-net .value {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--brand);
        }

        .slip-sign {
            margin-top: 2.25rem;
            font-size: 0.87rem;
        }

        .slip-sign .signee {
            font-weight: 700;
            margin-top: 2.5rem;
        }

        .slip-actions {
            max-width: 640px;
            margin: 0 auto 2rem;
            display: flex;
            gap: 0.6rem;
        }

        .btn-brand {
            background: var(--brand);
            border: none;
            color: #fff;
            font-weight: 600;
        }

        .btn-brand:hover {
            color: #fff;
            filter: brightness(1.08);
        }

        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            .slip-sheet { box-shadow: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
<?php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $bulanLabel      = $namaBulan[(int) $slip['bulan']] ?? esc((string) $slip['bulan']);
    $totalPenerimaan = (float) $slip['gaji_pokok'] + (float) $slip['tunjangan'] + (float) $slip['uang_lembur'];
    $totalPotongan   = (float) $slip['potongan'];
    $rp              = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
?>
<div class="slip-sheet">
    <div class="slip-kode">Kode Slip: <?= esc($slip['kode_slip']) ?></div>

    <div class="slip-heading">
        <div class="doc-title">Slip Gaji</div>
        <div class="periode">Periode <?= esc($bulanLabel) ?> <?= esc($slip['tahun']) ?></div>
        <div class="company">PT KECAP</div>
    </div>
    <hr class="slip-rule">

    <div class="slip-meta">
        <div><span class="lbl">Kode Kry</span><span class="sep">:</span><span><?= esc($slip['kode_karyawan']) ?></span></div>
        <div><span class="lbl">Nama</span><span class="sep">:</span><span><?= esc($slip['nama']) ?></span></div>
        <div><span class="lbl">Jabatan</span><span class="sep">:</span><span><?= esc($slip['nama_jabatan']) ?></span></div>
        <?php if (! empty($slip['nomor_rekening'])): ?>
        <div><span class="lbl">Bank</span><span class="sep">:</span><span><?= esc($slip['nama_bank']) ?> &ndash; <?= esc($slip['nomor_rekening']) ?></span></div>
        <?php endif; ?>
    </div>

    <div class="slip-columns">
        <div class="col-penerimaan">
            <h6>PENERIMAAN</h6>
            <div class="slip-line"><span>Gaji Pokok</span><span class="val"><?= $rp($slip['gaji_pokok']) ?></span></div>
            <div class="slip-line"><span>Tunjangan</span><span class="val"><?= $rp($slip['tunjangan']) ?></span></div>
            <div class="slip-line"><span>Uang Lembur</span><span class="val"><?= $rp($slip['uang_lembur']) ?></span></div>
            <div class="slip-total-line"><span>TOTAL PENERIMAAN</span><span class="val"><?= $rp($totalPenerimaan) ?></span></div>
        </div>
        <div class="col-potongan">
            <h6>POTONGAN</h6>
            <div class="slip-line"><span>Potongan</span><span class="val"><?= $rp($totalPotongan) ?></span></div>
            <div class="slip-total-line"><span>TOTAL POTONGAN</span><span class="val"><?= $rp($totalPotongan) ?></span></div>
        </div>
    </div>

    <div class="slip-net">
        <div class="label">Gaji Bersih (THP)</div>
        <div class="value"><?= $rp($slip['total_gaji']) ?></div>
    </div>

    <div class="slip-sign">
        <div>Tanggal dibayarkan: <?= esc($slip['tanggal_gaji']) ?></div>
        <div>Diterima Oleh,</div>
        <div class="signee"><?= esc($slip['nama']) ?></div>
    </div>
</div>

<div class="slip-actions no-print">
    <button onclick="window.print()" class="btn btn-brand"><i class="bi bi-printer-fill me-1"></i>Cetak / Print</button>
    <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-secondary">Kembali</a>
</div>
</body>
</html>