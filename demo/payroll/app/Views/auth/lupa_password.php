<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Sistem Penggajian PT Kecap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            width: 100%;
            max-width: 460px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(36, 27, 26, 0.18);
            background: #fff;
        }

        .login-main {
            padding: 2.75rem 2.5rem;
        }

        .login-title {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--ink);
            letter-spacing: -0.01em;
            margin-bottom: 0.35rem;
        }

        .login-subtitle {
            color: var(--muted);
            font-size: 0.88rem;
            margin-bottom: 1.75rem;
            line-height: 1.55;
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
            font-size: 0.8rem;
            margin-top: 1.25rem;
            text-align: center;
        }

        .login-footnote a {
            color: var(--brand-red);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="login-main">
        <div class="login-title">Lupa Password?</div>
        <div class="login-subtitle">Fitur ini cuma buat akun <strong>Admin</strong> dan <strong>Karyawan</strong>. Pilih peran Anda dan masukkan username, nanti yang berwenang akan menerima notifikasi untuk mengatur ulang password Anda.</div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form action="<?= site_url('lupa-password') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Peran</label>
                <?php $peran = old('peran'); ?>
                <select name="peran" class="form-select" required>
                    <option value="">-- Pilih Peran --</option>
                    <option value="Admin" <?= $peran === 'Admin' ? 'selected' : '' ?>>Admin (diproses oleh Owner)</option>
                    <option value="Karyawan" <?= $peran === 'Karyawan' ? 'selected' : '' ?>>Karyawan (diproses oleh Admin)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= esc(old('username')) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="catatan" class="form-control" rows="2" maxlength="255" placeholder="Contoh: lupa password setelah ganti HP"><?= esc(old('catatan')) ?></textarea>
            </div>

            <button type="submit" class="btn btn-login w-100">Kirim Permintaan</button>
        </form>

        <div class="login-footnote"><a href="<?= site_url('login') ?>">&larr; Kembali ke halaman login</a></div>
    </div>
</div>
</body>
</html>
