<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GestiStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Manrope', sans-serif;
            background: radial-gradient(circle at top left, rgba(34, 197, 94, .14), transparent 28%),
                        radial-gradient(circle at top right, rgba(245, 158, 11, .14), transparent 24%),
                        linear-gradient(180deg, #020617 0%, #101418 100%);
            color: #f8fafc;
            padding: 18px;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background: rgba(16, 20, 24, .94);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 28px 80px rgba(0,0,0,.25);
        }
        .auth-card h1 {
            font-size: 2rem;
            margin-bottom: 12px;
        }
        .form-control {
            border-radius: 14px;
            background: #0f172a;
            border: 1px solid rgba(255,255,255,.08);
            color: #f8fafc;
            min-height: 52px;
        }
        .form-label { color: #cbd5e1; }
        .btn-primary {
            width: 100%;
            border-radius: 14px;
            background: linear-gradient(135deg,#22c55e,#4ade80);
            border: transparent;
            color: #031b0c;
            font-weight: 700;
            min-height: 52px;
        }
        .text-muted { color: #94a3b8; }
        .alert { border-radius: 16px; }
        a { color: #86efac; text-decoration: none; }
    </style>
</head>
<body>
<div class="auth-card">
    <h1>Connexion</h1>
    <p class="text-muted mb-4">Accédez à votre boutique et gérez vos ventes en toute sécurité.</p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= base_url('login') ?>" method="POST">
        <div class="mb-3">
            <label class="form-label">Adresse e-mail</label>
            <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <div class="text-center mt-4 text-muted">
        Pas encore de compte ? <a href="<?= base_url('register') ?>">Créer un compte</a>
    </div>
</div>
</body>
</html>