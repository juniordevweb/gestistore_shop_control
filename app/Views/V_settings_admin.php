<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres Admin - GestiStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #101418;
            --bg-panel: #171c22;
            --bg-card: #1d242c;
            --bg-soft: #252d36;
            --text-main: #f5f7fa;
            --text-muted: #98a2b3;
            --green: #22c55e;
            --green-soft: rgba(34, 197, 94, .16);
            --orange: #f59e0b;
            --orange-soft: rgba(245, 158, 11, .16);
            --red: #ef4444;
            --red-soft: rgba(239, 68, 68, .16);
            --blue: #38bdf8;
            --blue-soft: rgba(56, 189, 248, .16);
            --border-soft: rgba(255, 255, 255, .06);
            --nav-height: 72px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, .10), transparent 28%),
                radial-gradient(circle at top right, rgba(245, 158, 11, .10), transparent 24%),
                linear-gradient(180deg, #0d1117 0%, var(--bg-main) 100%);
            color: var(--text-main);
            padding: 18px 14px calc(var(--nav-height) + 18px);
            overflow-x: hidden;
        }

        .page-shell {
            max-width: 600px;
            margin: 0 auto;
        }

        .glass-panel {
            background: rgba(15, 23, 42, .88);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            padding: 18px;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            margin-bottom: 18px;
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out;
        }

        .glass-panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid var(--border-soft);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease-out;
        }

        .stat-card:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(34, 197, 94, .1);
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--green);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .period-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 16px;
        }

        .period-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }

        .period-metric {
            background: rgba(255, 255, 255, .04);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 14px;
        }

        .period-metric .metric-label {
            color: var(--text-muted);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }

        .period-metric .metric-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #fff;
        }

        .period-metric.revenue .metric-value {
            color: var(--green);
        }

        .period-metric.sales .metric-value {
            color: var(--blue);
        }

        .period-metric.recette .metric-value {
            color: var(--orange);
        }

        .period-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .period-actions {
            margin-top: 14px;
        }

        .period-note {
            margin-top: 10px;
            color: var(--text-muted);
            font-size: .82rem;
        }

        @media (max-width: 576px) {
            .period-summary,
            .period-form {
                grid-template-columns: 1fr;
            }
        }

        .form-control {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, .06);
            color: #fff;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 0.2rem rgba(34, 197, 94, .25);
        }

        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #22c55e, #4ade80);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, .3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            border: none;
            color: #92400e;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #f87171);
            border: none;
        }

        .table {
            color: var(--text-main);
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--bg-soft);
            border-bottom: 1px solid var(--border-soft);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, .02);
        }

        .badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 600;
        }

        .alert-success {
            background: var(--green-soft);
            color: #86efac;
        }

        .alert-danger {
            background: var(--red-soft);
            color: #fca5a5;
        }

        .request-card {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid var(--border-soft);
        }

        .request-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .request-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: #fff;
        }

        .request-meta {
            color: var(--text-muted);
            font-size: .82rem;
            margin-top: 4px;
            overflow-wrap: anywhere;
        }

        .request-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .request-actions form {
            margin: 0;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
        }

        .status-pending {
            background: rgba(245, 158, 11, .15);
            color: #fcd34d;
        }

        .status-approved {
            background: rgba(34, 197, 94, .15);
            color: #a7f3c0;
        }

        .status-rejected {
            background: rgba(239, 68, 68, .15);
            color: #ffb4b4;
        }

        @media (max-width: 576px) {
            .page-shell {
                padding: 0 8px;
            }
            .glass-panel {
                padding: 14px;
            }
            .section-title {
                font-size: 1.1rem;
            }
        }

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
            -webkit-backdrop-filter: blur(18px);
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
            transition: .25s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .bn-item i {
            font-size: 1.2rem;
            color: #94a3b8;
        }

        .bn-item span {
            margin-top: 4px;
            font-size: clamp(.55rem, 2.5vw, .68rem);
            font-weight: 700;
            color: #94a3b8;
            white-space: normal;
            line-height: 1.1;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .bn-item.active {
            background: rgba(34, 197, 94, .12);
        }

        .bn-item.active i,
        .bn-item.active span {
            color: #22c55e;
        }

        .bn-dot {
            position: absolute;
            top: 6px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #22c55e;
            opacity: 0;
        }

        .bn-item.active .bn-dot {
            opacity: 1;
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

        @media (min-width: 1200px) {
            .bottom-nav {
                width: 580px;
            }

            .bn-item span {
                font-size: .72rem;
            }
        }

        @media (max-width: 360px) {
            :root {
                --nav-height: 76px;
            }

            .bottom-nav {
                padding-left: 4px;
                padding-right: 4px;
            }

            .bn-item {
                padding: 8px 2px 6px;
            }
        }
    </style>
</head>
<body>
<div class="page-shell">

    <!-- HEADER -->
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-crown text-warning"></i>
            Paramètres Administrateur
        </div>
        <p class="text-muted mb-0">Gérez les utilisateurs et la configuration globale</p>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- DEMANDES DE VALIDATION -->
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-bell text-warning"></i>
            Demandes de validation
        </div>

        <?php if (! empty($pending_users)): ?>
            <?php foreach ($pending_users as $pendingUser): ?>
                <div class="request-card">
                    <div class="request-head">
                        <div>
                            <p class="request-title"><?= esc($pendingUser['email']) ?></p>
                            <div class="request-meta">
                                Shop ID: <?= esc($pendingUser['shop_id']) ?><br>
                                Cree le: <?= esc($pendingUser['created_at'] ?? '--') ?>
                            </div>
                        </div>
                        <span class="status-pill status-pending">En attente</span>
                    </div>

                    <div class="request-actions">
                        <form action="<?= base_url('settings/approveUser/' . $pendingUser['id']) ?>" method="post">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-check me-1"></i>
                                Confirmer
                            </button>
                        </form>

                        <form action="<?= base_url('settings/rejectUser/' . $pendingUser['id']) ?>" method="post">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-xmark me-1"></i>
                                Annule
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="stat-card">
                <div class="stat-label">Aucune nouvelle demande en attente.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- GESTION UTILISATEURS -->
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-users text-info"></i>
            Gestion des Utilisateurs
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Shop ID</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong>#<?= $user['id'] ?></strong></td>
                            <td>
                                <i class="fa-solid fa-envelope me-1 text-muted"></i>
                                <?= esc($user['email']) ?>
                            </td>
                            <td>
                                <code class="text-muted"><?= esc($user['shop_id']) ?></code>
                            </td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="fa-solid fa-crown me-1"></i>Admin
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-store me-1"></i>Boutiquier
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $status = $user['account_status'] ?? 'approved'; ?>
                                <span class="status-pill status-<?= esc($status) ?>">
                                    <?= esc(ucfirst($status)) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STATISTIQUES GLOBALES -->
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-chart-line text-success"></i>
            Statistiques Globales
        </div>

        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?= $stats['total_shops'] ?? 0 ?></div>
                    <div class="stat-label">Boutiques</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?= $stats['total_products'] ?? 0 ?></div>
                    <div class="stat-label">Produits</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?= $stats['total_sales'] ?? 0 ?></div>
                    <div class="stat-label">Ventes</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card text-center">
                    <div class="stat-value"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') ?> FCFA</div>
                    <div class="stat-label">Chiffre d'Affaires</div>
                </div>
            </div>
        </div>
    </div>

    <!-- RAPPORT DE VENTES -->
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-chart-simple text-warning"></i>
            Rapport de ventes
        </div>

        <form method="get" action="<?= base_url('settings') ?>" class="period-panel">
            <div class="period-form">
                <div>
                    <label class="form-label fw-bold">
                        <i class="fa-solid fa-calendar-day me-1"></i>
                        Date de début
                    </label>
                    <input type="date" name="from" class="form-control" value="<?= esc($sales_period['from'] ?? '') ?>">
                </div>
                <div>
                    <label class="form-label fw-bold">
                        <i class="fa-solid fa-calendar-check me-1"></i>
                        Date de fin
                    </label>
                    <input type="date" name="to" class="form-control" value="<?= esc($sales_period['to'] ?? '') ?>">
                </div>
            </div>

            <div class="period-actions">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-2"></i>
                    Calculer le rapport
                </button>
            </div>

            <div class="period-summary">
                <div class="period-metric revenue">
                    <div class="metric-label">Total vendu</div>
                    <div class="metric-value"><?= number_format((float) ($sales_period['total'] ?? 0), 0, ',', ' ') ?> FCFA</div>
                </div>
                <div class="period-metric recette">
                    <div class="metric-label">Total recette</div>
                    <div class="metric-value"><?= number_format((float) ($sales_period['recette'] ?? 0), 0, ',', ' ') ?> FCFA</div>
                </div>
                <div class="period-metric sales">
                    <div class="metric-label">Nombre de ventes</div>
                    <div class="metric-value"><?= (int) ($sales_period['count'] ?? 0) ?></div>
                </div>
            </div>

            <div class="period-note">
                Période affichée: du <strong><?= esc($sales_period['from'] ?? '') ?></strong> au <strong><?= esc($sales_period['to'] ?? '') ?></strong>.
                La date de début peut rester vide pour prendre la première vente enregistrée.
            </div>
        </form>
    </div>

    <!-- PARAMÈTRES SYSTÈME -->
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-sliders-h text-primary"></i>
            Paramètres Système
        </div>

        <form>
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-money-bill-wave me-1"></i>
                    Devise par défaut
                </label>
                <select class="form-select">
                    <option value="FCFA">FCFA - Franc CFA</option>
                    <option value="EUR">EUR - Euro</option>
                    <option value="USD">USD - Dollar</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Seuil d'alerte stock
                </label>
                <input type="number" class="form-control" value="5" min="1">
                <div class="form-text text-muted">Nombre minimum avant alerte</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="fa-solid fa-clock me-1"></i>
                    Fuseau horaire
                </label>
                <select class="form-select">
                    <option>Africa/Dakar (UTC+0)</option>
                    <option>Europe/Paris (UTC+1)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fa-solid fa-save me-2"></i>
                Sauvegarder les paramètres
            </button>
        </form>
    </div>

</div>

<nav class="bottom-nav" aria-label="Navigation principale">
    <a href="<?= base_url('/') ?>" class="bn-item">
        <i class="fa-solid fa-border-all" aria-hidden="true"></i>
        <div class="bn-dot"></div>
        <span>Accueil</span>
    </a>
    <a href="<?= base_url('/stock') ?>" class="bn-item">
        <i class="fa-solid fa-cube" aria-hidden="true"></i>
        <div class="bn-dot"></div>
        <span>Stock</span>
    </a>
    <a href="<?= base_url('sales/create') ?>" class="bn-item">
        <i class="fa-solid fa-receipt" aria-hidden="true"></i>
        <div class="bn-dot"></div>
        <span>Ventes</span>
    </a>
    <a href="<?= base_url('/debts') ?>" class="bn-item">
        <i class="fa-solid fa-users" aria-hidden="true"></i>
        <div class="bn-dot"></div>
        <span>Dettes</span>
    </a>
    <a href="<?= base_url('settings') ?>" class="bn-item active" aria-current="page">
        <i class="fa-solid fa-gear" aria-hidden="true"></i>
        <div class="bn-dot"></div>
        <span>Reglages</span>
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
