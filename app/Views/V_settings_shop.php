<!DOCTYPE html>
<html lang="fr">
<head>
    <?= view('pwa_head') ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres Boutique - GestiStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #101418;
            --bg-panel: #171c22;
            --bg-card: #1d242c;
            --border-soft: rgba(255, 255, 255, .06);
            --text-main: #f5f7fa;
            --text-muted: #98a2b3;
            --green: #22c55e;
            --green-soft: rgba(34, 197, 94, .16);
            --orange: #f59e0b;
            --orange-soft: rgba(245, 158, 11, .16);
            --red-soft: rgba(239, 68, 68, .16);
            --nav-height: 72px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, .10), transparent 28%),
                radial-gradient(circle at top right, rgba(245, 158, 11, .10), transparent 24%),
                linear-gradient(180deg, #0d1117 0%, var(--bg-main) 100%);
            color: var(--text-main);
            padding: 18px 14px calc(var(--nav-height) + 18px);
            overflow-x: hidden;
        }

        .page-shell { max-width: 600px; margin: 0 auto; }
        .glass-panel {
            background: rgba(15, 23, 42, .88);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            padding: 18px;
            margin-bottom: 18px;
            backdrop-filter: blur(14px);
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control, .form-select {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, .06);
            color: #fff;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, .25);
            background: #0f172a;
            color: #fff;
        }

        .form-label {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-text { color: var(--text-muted); font-size: .8rem; }

        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, #22c55e, #4ade80);
            border: none;
            color: #031b0c;
        }

        .btn-success {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border: none;
            color: #f0fdf4;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 600;
        }

        .alert-success { background: var(--green-soft); color: #86efac; }
        .alert-danger { background: var(--red-soft); color: #fca5a5; }

        .stat-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 16px;
            border: 1px solid var(--border-soft);
        }

        .period-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 16px;
        }

        .period-form,
        .period-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .period-summary { margin-top: 14px; }

        .period-metric {
            background: rgba(255, 255, 255, .04);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 14px;
        }

        .metric-label {
            color: var(--text-muted);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }

        .metric-value {
            font-size: 1.1rem;
            font-weight: 800;
            color: #fff;
        }

        .period-metric.revenue .metric-value { color: var(--green); }
        .period-metric.recette .metric-value { color: var(--orange); }
        .period-metric.dette .metric-value { color: var(--red); }
        .period-metric.sales .metric-value { color: #38bdf8; }

        .period-note { margin-top: 10px; color: var(--text-muted); font-size: .82rem; }
        .period-actions { margin-top: 14px; }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            max-width: 100vw;
            height: var(--nav-height);
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            align-items: center;
            padding: 8px 10px env(safe-area-inset-bottom);
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(18px);
            border-top: 1px solid rgba(255, 255, 255, .06);
            z-index: 9999;
            overflow: hidden;
        }

        .bn-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 0;
            padding: 8px 4px;
            border-radius: 18px;
            text-decoration: none;
            position: relative;
        }

        .bn-item i { font-size: 1.2rem; color: #94a3b8; }
        .bn-item span {
            margin-top: 4px;
            font-size: clamp(.55rem, 2.5vw, .68rem);
            font-weight: 700;
            color: #94a3b8;
            line-height: 1.1;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .bn-item.active { background: rgba(34, 197, 94, .12); }
        .bn-item.active i, .bn-item.active span { color: #22c55e; }

        .bn-dot {
            position: absolute;
            top: 6px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #22c55e;
            opacity: 0;
        }

        .bn-item.active .bn-dot { opacity: 1; }

        @media (max-width: 576px) {
            .period-form, .period-summary { grid-template-columns: 1fr; }
        }

        @media (min-width: 768px) {
            .bottom-nav {
                width: 520px;
                left: 50%;
                transform: translateX(-50%);
                bottom: 18px;
                border-radius: 24px;
                border: 1px solid rgba(255, 255, 255, .08);
                box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
            }
        }
    </style>
</head>
<body>
<?php $shopProfile = $shop_profile ?? []; ?>
<div class="page-shell">

    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-store text-primary"></i>
            Paramètres de la Boutique
        </div>
        <p class="text-muted mb-0">Personnalisez votre espace de vente</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-user-circle text-info"></i>
            Mon Profil
        </div>

        <form action="<?= base_url('settings/updateProfile') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-envelope"></i> Adresse e-mail</label>
                <input type="email" name="email" class="form-control" value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
                <div class="form-text">Utilisée pour la connexion et les notifications</div>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-2"></i>Mettre à jour le profil</button>
        </form>
    </div>

    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-shield-alt text-warning"></i>
            Sécurité du compte
        </div>

        <form action="<?= base_url('settings/changePassword') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-lock"></i> Mot de passe actuel</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-key"></i> Nouveau mot de passe</label>
                <input type="password" name="new_password" class="form-control" minlength="6" required>
                <div class="form-text">Minimum 6 caractères</div>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-check-double"></i> Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-refresh me-2"></i>Changer le mot de passe</button>
        </form>
    </div>

    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-building text-success"></i>
            Informations de la Boutique
        </div>

        <form action="<?= base_url('settings/updateShopInfo') ?>" method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-tag"></i> Nom de la boutique</label>
                <input type="text" name="shop_name" class="form-control" value="<?= esc(old('shop_name', $shopProfile['shop_name'] ?? 'Ma Super Boutique')) ?>" placeholder="Entrez le nom de votre boutique">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-map-marker-alt"></i> Adresse</label>
                <input type="text" name="address" class="form-control" value="<?= esc(old('address', $shopProfile['address'] ?? '')) ?>" placeholder="Adresse complète">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-phone"></i> Téléphone</label>
                <input type="tel" name="phone" class="form-control" value="<?= esc(old('phone', $shopProfile['phone'] ?? '')) ?>" placeholder="+221 XX XXX XX XX">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-globe"></i> Site web (optionnel)</label>
                <input type="url" name="website" class="form-control" value="<?= esc(old('website', $shopProfile['website'] ?? '')) ?>" placeholder="https://www.maboutique.com">
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save me-2"></i>Sauvegarder les informations</button>
        </form>
    </div>

    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-chart-column text-warning"></i>
            Rapport mensuel
        </div>
        <p class="text-muted mb-3">Voir le chiffre d'affaires et le bénéfice jour par jour pour le mois en cours.</p>
        <a href="<?= base_url('settings/revenue') ?>" class="btn btn-primary w-100">
            <i class="fa-solid fa-eye me-2"></i>
            Voir chiffre d'affaires
        </a>
    </div>

</div>

<nav class="bottom-nav" aria-label="Navigation principale">
    <a href="<?= base_url('/') ?>" class="bn-item">
        <i class="fa-solid fa-border-all"></i><div class="bn-dot"></div><span>Accueil</span>
    </a>
    <a href="<?= base_url('/stock') ?>" class="bn-item">
        <i class="fa-solid fa-cube"></i><div class="bn-dot"></div><span>Stock</span>
    </a>
    <a href="<?= base_url('sales/create') ?>" class="bn-item">
        <i class="fa-solid fa-receipt"></i><div class="bn-dot"></div><span>Ventes</span>
    </a>
    <a href="<?= base_url('/debts') ?>" class="bn-item">
        <i class="fa-solid fa-users"></i><div class="bn-dot"></div><span>Dettes</span>
    </a>
    <a href="<?= base_url('settings') ?>" class="bn-item active" aria-current="page">
        <i class="fa-solid fa-gear"></i><div class="bn-dot"></div><span>Réglages</span>
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= view('pwa_register') ?>
</body>
</html>
