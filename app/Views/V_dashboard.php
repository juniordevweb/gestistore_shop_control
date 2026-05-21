<?php
$recentSales = $recent_sales ?? [
    ['client' => 'Awa Boutique', 'produit' => 'Riz 25kg', 'montant' => 18500, 'heure' => '09:15'],
    ['client' => 'Mamadou Market', 'produit' => 'Huile 5L', 'montant' => 32000, 'heure' => '11:40'],
    ['client' => 'Fatou Express', 'produit' => 'Sucre 1kg', 'montant' => 12750, 'heure' => '14:05'],
];

$activeDebts = $active_debts ?? [
    ['client' => 'Mamadou Diallo', 'montant' => 125000, 'type' => 'Dette automobile', 'status' => 'En retard', 'class' => 'danger', 'icon' => 'fa-user'],
    ['client' => 'Fatou Ndiaye', 'montant' => 80000, 'type' => 'Location vehicule', 'status' => 'En attente', 'class' => 'warning', 'icon' => 'fa-user'],
    ['client' => 'Alioune Sarr', 'montant' => 210000, 'type' => 'Credit valide', 'status' => 'Partiel', 'class' => 'success', 'icon' => 'fa-user'],
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestiStore Senegal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
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
            --nav-h: 68px;
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
            /* espace pour la bottom nav fixe */
            padding-bottom: var(--nav-h);
        }

        /* ───── BOTTOM NAVIGATION ───── */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 100vw;
            height: var(--nav-h);
            background: rgba(18, 22, 28, 0.96);
            border-top: 1px solid var(--border-soft);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            align-items: center;
            z-index: 1000;
            padding: 0 4px;
            overflow: hidden;
        }

        .bn-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-decoration: none;
            min-width: 0;
            padding: 8px 4px 6px;
            border-radius: 16px;
            transition: background 0.18s;
            -webkit-tap-highlight-color: transparent;
        }

        .bn-item:active {
            background: rgba(255, 255, 255, .04);
        }

        .bn-item i {
            font-size: 1.25rem;
            color: var(--text-muted);
            transition: color 0.18s, transform 0.18s;
        }

        .bn-item span {
            font-size: clamp(0.55rem, 2.5vw, 0.68rem);
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.02em;
            transition: color 0.18s;
            line-height: 1.1;
            text-align: center;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        /* onglet actif */
        .bn-item.active i,
        .bn-item.active span {
            color: var(--green);
        }

        .bn-item.active i {
            transform: translateY(-2px);
        }

        /* petit dot indicateur */
        .bn-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--green);
            margin-top: -2px;
            opacity: 0;
            transition: opacity 0.18s;
        }

        .bn-item.active .bn-dot {
            opacity: 1;
        }

        /* ───── DASHBOARD SHELL ───── */
        .dashboard-shell {
            max-width: 560px;
            margin: 0 auto;
            padding: 24px 16px 32px;
        }

        .glass-panel {
            background: rgba(23, 28, 34, .88);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, .30);
            backdrop-filter: blur(10px);
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1 1 220px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
            flex: 1 1 100%;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        .brand-chip {
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(34, 197, 94, .26), rgba(56, 189, 248, .24));
            border: 1px solid rgba(255, 255, 255, .08);
            font-size: 1.2rem;
        }

        .eyebrow {
            font-size: .78rem;
            color: var(--text-muted);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .page-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .mini-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .04);
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: .8rem;
            min-width: 0;
            max-width: 100%;
            white-space: normal;
            overflow-wrap: anywhere;
            text-align: center;
            text-decoration: none;
        }

        .user-pill {
            flex: 1 1 220px;
        }

        .logout-pill {
            flex: 0 1 auto;
        }

        /* STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            border-radius: 24px;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .stat-card.sales {
            background: linear-gradient(180deg, rgba(34, 197, 94, .18), rgba(34, 197, 94, .09));
        }

        .stat-card.orders {
            background: linear-gradient(180deg, rgba(56, 189, 248, .18), rgba(56, 189, 248, .09));
        }

        .stat-card.products {
            background: linear-gradient(180deg, rgba(245, 158, 11, .18), rgba(245, 158, 11, .09));
        }

        .stat-card.alerts {
            background: linear-gradient(180deg, rgba(239, 68, 68, .18), rgba(239, 68, 68, .09));
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            margin-bottom: 18px;
            background: rgba(255, 255, 255, .10);
            color: #fff;
        }

        .stat-value {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .stat-label {
            margin-top: 6px;
            font-size: .86rem;
            color: var(--text-muted);
        }

        .stat-mini-wrap {
            margin-top: 12px;
            display: grid;
            gap: 10px;
        }

        .stat-mini-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .05);
        }

        .stat-mini-card .mini-label {
            font-size: .72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .stat-mini-card .mini-value {
            font-size: .95rem;
            font-weight: 800;
            color: #fff;
            white-space: nowrap;
        }

        .stat-mini-card.profit {
            background: linear-gradient(180deg, rgba(34, 197, 94, .18), rgba(34, 197, 94, .10));
        }

        /* ACTION */
        .action-card {
            display: block;
            text-decoration: none;
            color: #07130b;
            background: linear-gradient(135deg, #22c55e, #86efac);
            border-radius: 22px;
            padding: 18px 20px;
            font-weight: 800;
            margin-bottom: 22px;
        }

        /* SECTIONS */
        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .section-title h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        .section-title span {
            color: var(--text-muted);
            font-size: .8rem;
        }

        .list-stack {
            display: grid;
            gap: 12px;
        }

        .list-card,
        .sale-item,
        .debt-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 20px;
            padding: 14px 15px;
        }

        .list-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .item-name,
        .sale-name,
        .debt-name {
            margin: 0;
            font-size: .95rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .item-meta,
        .sale-time,
        .debt-type {
            margin-top: 4px;
            font-size: .8rem;
            color: var(--text-muted);
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 700;
        }

        .stock-low {
            background: var(--red-soft);
            color: #ffb4b4;
        }

        .stock-ok {
            background: var(--green-soft);
            color: #a7f3c0;
        }

        .sale-item,
        .debt-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .sale-left,
        .debt-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sale-avatar,
        .debt-avatar {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            flex-shrink: 0;
        }

        .sale-avatar {
            background: rgba(56, 189, 248, .12);
            color: #7dd3fc;
        }

        .debt-avatar {
            background: rgba(239, 68, 68, .15);
            color: #fff;
        }

        .debt-avatar.warning {
            background: rgba(245, 158, 11, .15);
        }

        .debt-avatar.success {
            background: rgba(34, 197, 94, .15);
        }

        .debt-amount {
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .debt-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
        }

        .status-danger {
            background: rgba(239, 68, 68, .15);
            color: #ffb4b4;
        }

        .status-warning {
            background: rgba(245, 158, 11, .15);
            color: #fcd34d;
        }

        .status-success {
            background: rgba(34, 197, 94, .15);
            color: #a7f3c0;
        }

        .sales-summary {
            margin-top: 24px;
        }

        .debt-section {
            margin-top: 24px;
        }

        /* ─────────────────────────────
   MOBILE FIRST BOTTOM NAV
───────────────────────────── */

        :root {
            --nav-height: 72px;
        }

        /* espace pour la navbar */
        body {
            padding-bottom: calc(var(--nav-height) + 12px);
        }

        /* NAVBAR */
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

        /* ITEM */
        .bn-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-decoration: none;

            position: relative;

            padding: 8px 4px;

            border-radius: 18px;

            transition: .25s ease;

            min-width: 0;
        }

        /* ICON */
        .bn-item i {
            font-size: 1.2rem;
            color: #94a3b8;

            transition: .25s ease;
        }

        /* TEXT */
        .bn-item span {
            margin-top: 4px;

            font-size: clamp(.55rem, 2.5vw, .68rem);
            font-weight: 700;

            color: #94a3b8;

            transition: .25s ease;

            white-space: normal;
            line-height: 1.1;
            text-align: center;
            overflow-wrap: anywhere;
        }

        /* ACTIVE */
        .bn-item.active {
            background: rgba(34, 197, 94, .12);
        }

        .bn-item.active i,
        .bn-item.active span {
            color: #22c55e;
        }

        /* DOT */
        .bn-dot {
            position: absolute;
            top: 6px;

            width: 5px;
            height: 5px;

            border-radius: 50%;

            background: #22c55e;

            opacity: 0;

            transition: .25s ease;
        }

        .bn-item.active .bn-dot {
            opacity: 1;
        }

        /* TAP EFFECT */
        .bn-item:active {
            transform: scale(.96);
        }

        /* TABLET */
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

        /* DESKTOP */
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

        @media (max-width: 576px) {
            .header-row {
                align-items: stretch;
            }

            .brand-wrap {
                flex: 1 1 100%;
            }

            .header-actions {
                width: 100%;
                gap: 8px;
            }

            .user-pill,
            .logout-pill {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>

    <main class="dashboard-shell">
        <section class="glass-panel p-3 p-sm-4">

            <!-- EN-TETE -->
            <div class="header-row">
                <div class="brand-wrap">
                    <div class="brand-chip">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div class="eyebrow">Dashboard SaaS</div>
                        <h1 class="page-title">GestiStore</h1>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="mini-pill user-pill">
                        <i class="fa-solid fa-user me-1"></i> <?= esc(session()->get('email')) ?>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="mini-pill logout-pill text-danger">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Déconnexion
                    </a>
                </div>
            </div>

            <!-- STATS -->
            <div class="stats-grid">
                <article class="stat-card sales">
                    <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    <p class="stat-value"><?= number_format((float) ($daily_sales ?? 0), 0, ',', ' ') ?> FCFA</p>
                    <div class="stat-label">Chiffre d'affaires du jour</div>
                    <div class="stat-mini-wrap">
                        <div class="stat-mini-card profit">
                            <div>
                                <div class="mini-label">Bénéfice du jour</div>
                                <div class="mini-value"><?= number_format((float) ($daily_profit ?? 0), 0, ',', ' ') ?> FCFA</div>
                            </div>
                            <i class="fa-solid fa-arrow-trend-up text-success"></i>
                        </div>
                    </div>
                </article>
                <article class="stat-card orders">
                    <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                    <p class="stat-value"><?= $total_sales ?? 0 ?></p>
                    <div class="stat-label">Nombre de ventes</div>
                </article>
                <article class="stat-card products">
                    <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <p class="stat-value"><?= $total_products ?? 0 ?></p>
                    <div class="stat-label">Produits en stock</div>
                </article>
                <article class="stat-card alerts">
                    <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <p class="stat-value"><?= $low_stock ?? 0 ?></p>
                    <div class="stat-label">Produits en alerte</div>
                </article>
            </div>

            <!-- NOUVELLE VENTE -->
            <a href="<?= base_url('sales/create') ?>" class="action-card">
                <i class="fa-solid fa-plus me-2"></i>
                Nouvelle vente rapide
            </a>

          <!-- PRODUITS -->
<section>
    <div class="section-title">
        <h2>
            <i class="fa-solid fa-box-open me-2 text-warning"></i>
            Liste produits
        </h2>

        <span>
            <?= is_array($products ?? null) ? count($products) : 0 ?> articles
        </span>
    </div>

    <div class="list-stack">

        <?php if (!empty($products)): ?>

            <?php foreach (array_slice($products, 0, 3) as $p): ?>

                <?php $quantity = (float) ($p['quantite_display'] ?? 0); ?>

                <article class="list-card">

                    <div>
                        <p class="item-name">
                            <?= esc($p['nom'] ?? 'Produit') ?>
                        </p>

                        <div class="item-meta">
                            Quantite disponible : <?= number_format($quantity, 3, '.', ' ') ?> <?= esc($p['display_unit_label'] ?? 'g') ?>
                        </div>
                    </div>

                    <?php if ($quantity < 5): ?>

                        <span class="stock-badge stock-low">
                            <i class="fa-solid fa-arrow-trend-down"></i>
                            Stock faible
                        </span>

                    <?php else: ?>

                        <span class="stock-badge stock-ok">
                            <i class="fa-solid fa-circle-check"></i>
                            Disponible
                        </span>

                    <?php endif; ?>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <!-- VOIR TOUT -->
    <div class="text-center mt-3">
        <a href="<?= base_url('/stock') ?>"
           class="text-decoration-none fw-bold text-warning">
            Voir tous les produits →
        </a>
    </div>

</section>

         <!-- DETTES ACTIVES -->
<section class="debt-section">

    <div class="section-title">

        <h2>
            <i class="fa-solid fa-wallet me-2 text-danger"></i>
            Dettes actives
        </h2>

        <span><?= count($activeDebts) ?> clients</span>

    </div>

    <div class="list-stack">

        <?php foreach (array_slice($activeDebts, 0, 3) as $debt): ?>

            <article class="debt-card">

                <div class="debt-left">

                    <div class="debt-avatar <?= esc($debt['class'] ?? 'danger') ?>">
                        <i class="fa-solid <?= esc($debt['icon'] ?? 'fa-user') ?>"></i>
                    </div>

                    <div>

                        <p class="debt-name">
                            <?= esc($debt['client']) ?>
                        </p>

                        <div class="debt-type">
                            <?= esc($debt['type'] ?? 'Dette client') ?>
                        </div>

                    </div>

                </div>

                <div class="text-end">

                    <div class="debt-amount">
                        <?= number_format((float) $debt['montant'], 0, ',', ' ') ?>
                        FCFA
                    </div>

                    <span class="debt-status status-<?= esc($debt['class'] ?? 'danger') ?>">
                        <?= esc($debt['status'] ?? 'En attente') ?>
                    </span>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <!-- VOIR TOUT -->
    <div class="text-center mt-3">
        <a href="<?= base_url('/debts') ?>"
           class="text-decoration-none fw-bold text-danger">
            Voir toutes les dettes →
        </a>
    </div>

</section>
           <!-- DERNIERES VENTES -->
<section class="sales-summary">

    <div class="section-title">

        <h2>
            <i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>
            Dernieres ventes
        </h2>

        <span>Aujourd'hui</span>

    </div>

    <div class="list-stack">

        <?php foreach (array_slice($recentSales, 0, 3) as $sale): ?>

            <article class="sale-item">

                <div class="sale-left">

                    <div class="sale-avatar">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>

                    <div>

                        <p class="sale-name">
                            <?= esc($sale['client'] ?? 'Client') ?>
                        </p>

                        <div class="sale-time">
                            <?= esc($sale['produit'] ?? 'Produit inconnu') ?> •
                            <?= esc($sale['heure'] ?? '--:--') ?>
                        </div>

                    </div>

                </div>

                <div class="sale-amount">

                    <?= number_format((float) ($sale['montant'] ?? 0), 0, ',', ' ') ?>
                    FCFA

                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <!-- VOIR TOUT -->
    <div class="text-center mt-3">
        <a href="<?= base_url('/sales') ?>"
           class="text-decoration-none fw-bold text-success">
            Voir toutes les ventes →
        </a>
    </div>

</section>

        </section>
    </main>

    <!-- ───── BOTTOM NAVIGATION ───── -->
    <nav class="bottom-nav" aria-label="Navigation principale">

        <a href="#" class="bn-item active" aria-current="page">
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
            <i class="fa-solid fa-receipt"></i>
            <div class="bn-dot"></div>
            <span>Ventes</span>
        </a>

        <a href="<?= base_url('/debts') ?>" class="bn-item">
            <i class="fa-solid fa-users" aria-hidden="true"></i>
            <div class="bn-dot"></div>
            <span>Dettes</span>
        </a>

        <a href="<?= base_url('settings') ?>" class="bn-item">
            <i class="fa-solid fa-gear" aria-hidden="true"></i>
            <div class="bn-dot"></div>
            <span>Réglages</span>
        </a>

    </nav>

    <!-- JS pour activer l'onglet cliqué -->
    <script>
        document.querySelectorAll('.bn-item').forEach(function (item) {
            item.addEventListener('click', function () {
                document.querySelectorAll('.bn-item').forEach(function (el) {
                    el.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>

</body>

</html>
