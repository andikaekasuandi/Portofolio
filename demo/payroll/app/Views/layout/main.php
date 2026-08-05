<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Sistem Penggajian PT Kecap') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-red: #c1272d;
            --brand-red-light: #e0332f;
            --brand-red-dark: #7a1418;
            --sidebar-bg: #3d100d;
            --gold: #c98a3e;
            --gold-dark: #97631f;
            --ink: #241b1a;
            --muted: #82746f;
            --bg-app: #f7f4f2;
            --border-soft: #ece3e0;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            background: var(--bg-app);
            color: var(--ink);
        }

        ::selection {
            background: rgba(193, 39, 45, 0.18);
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(193, 39, 45, 0.18);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(193, 39, 45, 0.32);
        }

        a {
            color: var(--brand-red);
        }

        a:hover {
            color: var(--brand-red-dark);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand-red-light);
            box-shadow: 0 0 0 0.2rem rgba(193, 39, 45, 0.15);
        }

        .form-check-input:checked {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(193, 39, 45, 0.15);
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: 245px;
            min-height: 100vh;
            background: linear-gradient(165deg, var(--brand-red-dark) 0%, var(--sidebar-bg) 55%, #24080a 100%);
            color: #f1e7e6;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.2s ease;
            z-index: 1000;
        }

        body.sidebar-collapsed .sidebar {
            width: 72px;
        }

        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        body.sidebar-collapsed .sidebar-brand-text,
        body.sidebar-collapsed .sidebar-nav .nav-link .nav-label,
        body.sidebar-collapsed .sidebar-nav .nav-link .nav-badge,
        body.sidebar-collapsed .sidebar-user .user-info,
        body.sidebar-collapsed .sidebar-footer a.logout .logout-label {
            display: none;
        }

        body.sidebar-collapsed .sidebar-nav .nav-link {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        body.sidebar-collapsed .sidebar-user {
            justify-content: center;
        }

        body.sidebar-collapsed .sidebar-footer a.logout {
            justify-content: center;
        }

        body.sidebar-collapsed .main-wrapper {
            margin-left: 72px;
        }

        .sidebar::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            bottom: -80px;
            left: -60px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand-logo {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 4px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .sidebar-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-brand-text {
            line-height: 1.2;
        }

        .sidebar-brand-text .name {
            font-weight: 700;
            font-size: 1rem;
        }

        .sidebar-brand-text .sub {
            font-size: 0.72rem;
            opacity: 0.7;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0.75rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav .nav-link {
            color: #e8d4d1;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.62rem 0.85rem;
            border-radius: 9px;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
            border-left: 3px solid transparent;
            transition: background-color 0.15s ease, transform 0.15s ease, border-color 0.15s ease;
        }

        .sidebar-nav .nav-link i {
            font-size: 1rem;
            width: 1.1rem;
            text-align: center;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--gold);
            color: #fff;
            font-weight: 600;
        }

        .sidebar-nav .nav-link.active i {
            background: #fff;
            color: var(--brand-red-dark);
            border: 2px solid var(--gold);
            border-radius: 10px;
            width: 2.1rem;
            height: 2.1rem;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.85rem 1.1rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.35rem 0;
        }

        .sidebar-user .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--brand-red);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .sidebar-user .name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #fff;
        }

        .sidebar-user .role {
            font-size: 0.72rem;
            opacity: 0.65;
        }

        .sidebar-footer a.logout {
            color: #eadedd;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.1rem 0;
            text-decoration: none;
        }

        .sidebar-footer a.logout:hover {
            color: #fff;
        }

        /* ===== Main area ===== */
        .main-wrapper {
            margin-left: 245px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.2s ease;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #eceff1;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .topbar-search {
            flex: 1;
            max-width: 420px;
        }

        .topbar-search .form-control {
            background: #f4f5f7;
            border: none;
            padding-left: 2.5rem;
        }

        .topbar-search {
            position: relative;
        }

        .topbar-search i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa0a6;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .topbar-user .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-red-light), var(--brand-red-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .topbar-user .name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.1;
        }

        .topbar-user .role {
            font-size: 0.72rem;
            color: var(--muted);
        }

        .content-area {
            padding: 2rem;
            flex: 1;
        }

        /* ===== Hero banner (halaman Dashboard) ===== */
        .dashboard-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--brand-red-light) 0%, var(--brand-red-dark) 65%, var(--sidebar-bg) 100%);
            border-radius: var(--radius-lg);
            padding: 2rem 2.25rem;
            margin-bottom: 2rem;
            color: #fff;
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            right: -70px;
            top: -90px;
        }

        .dashboard-hero-eyebrow {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 0.6rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-hero-title {
            font-weight: 800;
            font-size: 1.7rem;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
            position: relative;
            z-index: 1;
        }

        .dashboard-hero-sub {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1.25rem;
            max-width: 46ch;
            position: relative;
            z-index: 1;
        }

        .dashboard-hero .btn-hero {
            background: #fff;
            color: var(--brand-red-dark);
            font-weight: 700;
            font-size: 0.88rem;
            padding: 0.55rem 1.1rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .dashboard-hero .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
            color: var(--brand-red-dark);
        }

        .stat-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-soft);
            padding: 1.1rem 1.25rem;
            height: 100%;
            transition: box-shadow 0.18s ease, transform 0.18s ease;
        }

        .stat-card:hover {
            box-shadow: 0 10px 24px rgba(36, 27, 26, 0.07);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 0.6rem;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.02em;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .stat-footer {
            border-top: 1px solid var(--border-soft);
            margin-top: 0.85rem;
            padding-top: 0.65rem;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--muted);
        }

        .panel-card {
            background: #fff;
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            height: 100%;
        }

        .panel-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
        }

        /* ===== Header halaman (dipakai di semua CRUD: judul + subjudul + tombol aksi) ===== */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .page-header-left {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
        }

        .page-header-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: rgba(193, 39, 45, 0.08);
            color: var(--brand-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .page-header-title {
            font-weight: 800;
            color: var(--ink);
            font-size: 1.6rem;
            letter-spacing: -0.02em;
            margin-bottom: 0.15rem;
        }

        .page-header-sub {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 0;
        }

        /* ===== Badge status semantik ===== */
        .badge-soft {
            font-weight: 600;
            font-size: 0.74rem;
            padding: 0.4em 0.75em;
            border-radius: 999px;
            letter-spacing: 0.01em;
        }

        .badge-soft-red { background: rgba(193, 39, 45, 0.1); color: var(--brand-red-dark); }
        .badge-soft-gold { background: rgba(201, 138, 62, 0.15); color: var(--gold-dark); }
        .badge-soft-green { background: rgba(27, 138, 90, 0.12); color: #146b45; }
        .badge-soft-blue { background: rgba(37, 99, 168, 0.1); color: #1d5590; }
        .badge-soft-amber { background: rgba(212, 143, 15, 0.14); color: #8a5c0a; }
        .badge-soft-gray { background: var(--bg-app); color: var(--muted); border: 1px solid var(--border-soft); }

        /* ===== Empty state ===== */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 1.8rem;
            color: rgba(193, 39, 45, 0.35);
            margin-bottom: 0.5rem;
            display: block;
        }

        /* ===== Override global untuk halaman CRUD (Kelola Jabatan/Karyawan/Absensi/Penggajian) ===== */
        .content-area h3 {
            font-weight: 700;
            color: var(--ink);
            font-size: 1.4rem;
        }

        .content-area .card {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-lg);
        }

        .content-area .card-body {
            padding: 1.5rem;
        }

        .content-area .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--muted);
            font-weight: 700;
            border-bottom: 1px solid var(--border-soft);
            white-space: nowrap;
        }

        .content-area .table td {
            font-size: 0.88rem;
            color: #3a3130;
            vertical-align: middle;
        }

        .content-area .table-hover > tbody > tr:hover > * {
            background-color: rgba(193, 39, 45, 0.035);
        }

        .content-area .btn {
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.88rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .content-area .btn:hover {
            transform: translateY(-1px);
        }

        .content-area .btn-primary {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
        }

        .content-area .btn-primary:hover {
            background-color: var(--brand-red-dark);
            border-color: var(--brand-red-dark);
        }

        .content-area .btn-outline-primary {
            color: var(--brand-red);
            border-color: var(--brand-red);
        }

        .content-area .btn-outline-primary:hover {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
        }

        .content-area .btn-success {
            background-color: #1b8a5a;
            border-color: #1b8a5a;
        }

        .content-area .btn-success:hover {
            background-color: #146b45;
            border-color: #146b45;
        }

        .content-area .badge.bg-secondary {
            background-color: var(--bg-app) !important;
            color: var(--muted) !important;
            font-weight: 500;
            border: 1px solid var(--border-soft);
        }

        .content-area .badge.bg-info {
            background-color: rgba(193, 39, 45, 0.1) !important;
            color: var(--brand-red) !important;
            font-weight: 500;
        }

        .content-area .alert-info {
            background-color: rgba(193, 39, 45, 0.06);
            border: 1px solid rgba(193, 39, 45, 0.2);
            color: var(--brand-red-dark);
        }

        .content-area .alert-success {
            background-color: rgba(27, 138, 90, 0.08);
            border: 1px solid rgba(27, 138, 90, 0.25);
            color: #146b45;
        }

        .content-area .alert-danger {
            background-color: rgba(220, 53, 69, 0.08);
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #b3202f;
        }

        .page-footer {
            text-align: center;
            font-size: 0.8rem;
            color: var(--muted);
            padding: 1.25rem;
        }

        /* ===== Animasi muncul konten dashboard saat halaman di-load ===== */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal-on-load {
            opacity: 0;
            animation: fadeSlideUp 0.55s ease forwards;
        }

        /* Halaman yang blok kontennya belum ditandai manual (reveal-on-load) tetap ikut animasi,
           dengan efek stagger otomatis berdasarkan urutan blok di halaman. */
        .content-reveal > *:not(.reveal-on-load) {
            opacity: 0;
            animation: fadeSlideUp 0.55s ease forwards;
        }
        .content-reveal > *:nth-child(1):not(.reveal-on-load) { animation-delay: .05s; }
        .content-reveal > *:nth-child(2):not(.reveal-on-load) { animation-delay: .12s; }
        .content-reveal > *:nth-child(3):not(.reveal-on-load) { animation-delay: .19s; }
        .content-reveal > *:nth-child(4):not(.reveal-on-load) { animation-delay: .26s; }
        .content-reveal > *:nth-child(5):not(.reveal-on-load) { animation-delay: .33s; }
        .content-reveal > *:nth-child(n+6):not(.reveal-on-load) { animation-delay: .4s; }

        /* ===== Panel "Tentang Perusahaan" yang isinya bisa di-scroll ===== */
        .about-scroll {
            max-height: 260px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .about-scroll p {
            font-size: 0.9rem;
            color: #3a3130;
            line-height: 1.65;
        }

        .about-scroll p:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<?php
    // Setelah login, sidebar dipaksa collapsed sekali; setelahnya ikut preferensi toggle user (localStorage)
    $forceSidebarCollapse = (bool) session()->get('force_sidebar_collapse');
    if ($forceSidebarCollapse) {
        session()->remove('force_sidebar_collapse');
    }
?>
<body<?= $forceSidebarCollapse ? ' class="sidebar-collapsed"' : '' ?>>
<script>
    <?php if ($forceSidebarCollapse): ?>
    localStorage.setItem('sidebarCollapsed', '1');
    <?php else: ?>
    // Terapkan state sidebar (collapsed/normal) sebelum konten tampil, biar nggak flicker
    if (localStorage.getItem('sidebarCollapsed') === '1') {
        document.body.classList.add('sidebar-collapsed');
    }
    <?php endif; ?>
</script>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-logo"><img src="<?= base_url('assets/img/logo-pt-kecap.png') ?>" alt="PT Kecap"></div>
        <div class="sidebar-brand-text">
            <div class="name">PT KECAP</div>
            <div class="sub">Sistem Penggajian</div>
        </div>
    </div>

    <?php
        // Aktif kalau URL sekarang persis sama, atau sub-halamannya (create/edit/delete/dst di bawah path yang sama)
        $isMenuActive = static function (string $path): bool {
            $target = site_url($path);

            return current_url() === $target || strpos(current_url(), $target . '/') === 0;
        };
    ?>
    <nav class="sidebar-nav">
        <a href="<?= site_url('dashboard') ?>" class="nav-link <?= $isMenuActive('dashboard') ? 'active' : '' ?>">
            <i class="bi bi-house-door-fill"></i> <span class="nav-label">Dashboard</span>
        </a>
        <?php if (session()->get('role') === 'Karyawan'): ?>
        <a href="<?= site_url('absensi-saya') ?>" class="nav-link <?= $isMenuActive('absensi-saya') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i> <span class="nav-label">Absensi Saya</span>
        </a>
        <a href="<?= site_url('gaji-saya') ?>" class="nav-link <?= $isMenuActive('gaji-saya') ? 'active' : '' ?>">
            <i class="bi bi-wallet-fill"></i> <span class="nav-label">Gaji Saya</span>
        </a>
        <?php else: ?>
        <a href="<?= site_url('jabatan') ?>" class="nav-link <?= $isMenuActive('jabatan') ? 'active' : '' ?>">
            <i class="bi bi-briefcase-fill"></i> <span class="nav-label">Kelola Jabatan</span>
        </a>
        <a href="<?= site_url('karyawan') ?>" class="nav-link <?= $isMenuActive('karyawan') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> <span class="nav-label">Kelola Karyawan</span>
        </a>
        <a href="<?= site_url('absensi') ?>" class="nav-link <?= $isMenuActive('absensi') ? 'active' : '' ?>">
            <i class="bi bi-calendar-check-fill"></i> <span class="nav-label">Kelola Absensi</span>
        </a>
        <a href="<?= site_url('penggajian') ?>" class="nav-link <?= $isMenuActive('penggajian') ? 'active' : '' ?>">
            <i class="bi bi-wallet-fill"></i> <span class="nav-label">Proses Penggajian</span>
        </a>
        <a href="<?= site_url('laporan/penggajian') ?>" class="nav-link <?= $isMenuActive('laporan/penggajian') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text-fill"></i> <span class="nav-label">Laporan Penggajian</span>
        </a>
        <a href="<?= site_url('akun-karyawan') ?>" class="nav-link <?= $isMenuActive('akun-karyawan') ? 'active' : '' ?>">
            <i class="bi bi-person-vcard-fill"></i> <span class="nav-label">Kelola Akun Karyawan</span>
            <?php if (session()->get('role') === 'Admin'): ?>
                <?php $jumlahLupaPasswordKaryawan = (new \App\Models\ResetPasswordRequestModel())->countPending('Karyawan'); ?>
                <?php if ($jumlahLupaPasswordKaryawan > 0): ?>
                    <span class="badge rounded-pill bg-danger ms-auto nav-badge"><?= $jumlahLupaPasswordKaryawan ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </a>
        <?php if (session()->get('role') === 'Owner'): ?>
        <?php $jumlahLupaPassword = (new \App\Models\ResetPasswordRequestModel())->countPending('Admin'); ?>
        <a href="<?= site_url('akun-admin') ?>" class="nav-link <?= $isMenuActive('akun-admin') ? 'active' : '' ?>">
            <i class="bi bi-person-badge-fill"></i> <span class="nav-label">Kelola Akun Admin</span>
            <?php if ($jumlahLupaPassword > 0): ?>
                <span class="badge rounded-pill bg-danger ms-auto nav-badge"><?= $jumlahLupaPassword ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= site_url('log-aktivitas') ?>" class="nav-link <?= $isMenuActive('log-aktivitas') ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i> <span class="nav-label">Log Aktivitas</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= esc(mb_substr(session()->get('nama') ?? 'U', 0, 1)) ?></div>
            <div class="user-info">
                <div class="name"><?= esc(session()->get('nama') ?? '-') ?></div>
                <div class="role"><?= esc(session()->get('role') ?? '-') ?></div>
            </div>
        </div>
        <a href="<?= site_url('logout') ?>" class="logout">
            <i class="bi bi-box-arrow-right"></i> <span class="logout-label">Logout</span>
        </a>
    </div>
</div>

<div class="main-wrapper">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button id="sidebarToggle" class="btn btn-light border-0" type="button">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="topbar-search">
                <i class="bi bi-search"></i>
                <input type="text" id="topbarSearch" class="form-control form-control-sm rounded-3 py-2" placeholder="Cari di halaman ini...">
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="avatar"><?= esc(mb_substr(session()->get('nama') ?? 'U', 0, 1)) ?></div>
                <div>
                    <div class="name"><?= esc(session()->get('nama') ?? '-') ?></div>
                    <div class="role"><?= esc(session()->get('role') ?? '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-area">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ((array) session()->getFlashdata('errors') as $pesanError): ?>
                        <li><?= esc($pesanError) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" onclick="this.closest('.alert').remove()" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <div class="content-reveal">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <div class="page-footer">
        &copy; <?= date('Y') ?> PT Kecap. All rights reserved.
    </div>
</div>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    sidebarToggle.addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(
            'sidebarCollapsed',
            document.body.classList.contains('sidebar-collapsed') ? '1' : '0'
        );
    });

    const topbarSearch = document.getElementById('topbarSearch');
    if (topbarSearch) {
        topbarSearch.addEventListener('input', function () {
            const kata = this.value.trim().toLowerCase();
            document.querySelectorAll('.content-area table tbody tr').forEach(function (baris) {
                if (baris.querySelector('.empty-state')) {
                    return;
                }
                baris.style.display = baris.textContent.toLowerCase().includes(kata) ? '' : 'none';
            });
        });
    }
</script>
</body>
</html>