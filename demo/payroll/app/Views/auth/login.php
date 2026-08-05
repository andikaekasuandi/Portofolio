<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Penggajian PT Kecap</title>
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
            --gold: #c98a3e;
            --ink: #241b1a;
            --muted: #8a7d79;
        }

        * {
            font-family: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7f4f2;
            padding: 1.5rem;
        }

        .login-shell {
            display: flex;
            width: 100%;
            max-width: 880px;
            min-height: 520px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(36, 27, 26, 0.18);
            background: #fff;
        }

        .login-aside {
            flex: 1 1 45%;
            position: relative;
            background: linear-gradient(160deg, var(--brand-red-light) 0%, var(--brand-red) 45%, var(--brand-red-dark) 100%);
            color: #fdece9;
            padding: 2.75rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .login-aside::before {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
            top: -140px;
            right: -120px;
        }

        .login-aside::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(201, 138, 62, 0.18);
            bottom: -90px;
            left: -60px;
        }

        .login-aside-top, .login-aside-bottom {
            position: relative;
            z-index: 1;
        }

        .login-aside-logo {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: #fff;
            padding: 6px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            margin-bottom: 1.75rem;
        }

        .login-aside-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-aside h2 {
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.01em;
            margin-bottom: 0.6rem;
        }

        .login-aside p {
            font-size: 0.9rem;
            color: rgba(255, 236, 233, 0.8);
            line-height: 1.6;
        }

        .login-aside-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            padding: 0.4rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 600;
            width: fit-content;
        }

        .login-aside-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--gold);
        }

        .login-main {
            flex: 1 1 55%;
            padding: 3rem 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--ink);
            letter-spacing: -0.01em;
            margin-bottom: 0.35rem;
        }

        .login-subtitle {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--ink);
        }

        .form-control {
            border: 1px solid #e7dedb;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.92rem;
        }

        .form-control:focus {
            border-color: var(--brand-red-light);
            box-shadow: 0 0 0 0.2rem rgba(193, 39, 45, 0.14);
        }

        .password-toggle {
            border: 1px solid #e7dedb;
            border-left: none;
            background: #fff;
            color: var(--muted);
            border-radius: 0 10px 10px 0;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            background: #fff;
            color: var(--brand-red);
        }

        .input-group:focus-within .form-control,
        .input-group:focus-within .password-toggle {
            border-color: var(--brand-red-light);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-red-light), var(--brand-red-dark));
            border: none;
            color: #fff;
            font-weight: 700;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            transition: box-shadow 0.15s ease, transform 0.15s ease;
        }

        .btn-login:hover {
            box-shadow: 0 10px 22px rgba(193, 39, 45, 0.28);
            color: #fff;
            transform: translateY(-1px);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.08);
            border: 1px solid rgba(220, 53, 69, 0.25);
            color: #b3202f;
            font-size: 0.85rem;
        }

        .alert-success {
            background-color: rgba(27, 138, 90, 0.08);
            border: 1px solid rgba(27, 138, 90, 0.25);
            color: #146b45;
            font-size: 0.85rem;
        }

        .login-footnote {
            font-size: 0.76rem;
            color: var(--muted);
            margin-top: 1.5rem;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .login-aside {
                display: none;
            }

            .login-main {
                padding: 2.25rem 1.75rem;
            }
        }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="login-aside">
        <div class="login-aside-top">
            <div class="login-aside-logo">
                <img src="<?= base_url('assets/img/logo-pt-kecap.png') ?>" alt="PT Kecap">
            </div>
            <h2>Sistem Informasi<br>Penggajian PT Kecap</h2>
            <p>Kelola data karyawan, absensi, dan proses penggajian dalam satu tempat yang rapi dan terintegrasi.</p>
        </div>
        <div class="login-aside-bottom">
            <div class="login-aside-badge"><span class="dot"></span>Akses Owner &middot; Admin</div>
        </div>
    </div>

    <div class="login-main">
        <div class="login-title">Selamat Datang Kembali</div>
        <div class="login-subtitle">Masuk dengan akun yang terdaftar untuk melanjutkan.</div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <form action="<?= site_url('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Tampilkan password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3 text-end">
                <a href="<?= site_url('lupa-password') ?>" style="font-size: 0.83rem; color: var(--brand-red); text-decoration: none; font-weight: 600;">Lupa Password?</a>
            </div>
            <button type="submit" class="btn btn-login w-100">Masuk</button>
        </form>

        <div class="login-footnote">&copy; <?= date('Y') ?> PT Kecap. All rights reserved.</div>
    </div>
</div>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var password = document.getElementById('password');
        var icon = this.querySelector('i');
        var tampil = password.type === 'password';

        password.type = tampil ? 'text' : 'password';
        icon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
        this.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
    });
</script>
</body>
</html>
