<?php
$recentSales = $recent_sales ?? [
    [
        'client' => 'Awa Boutique',
        'montant' => 18500,
        'heure'   => '09:15',
    ],
    [
        'client' => 'Mamadou Market',
        'montant' => 32000,
        'heure'   => '11:40',
    ],
    [
        'client' => 'Fatou Express',
        'montant' => 12750,
        'heure'   => '14:05',
    ],
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
            --green-soft: rgba(34, 197, 94, 0.16);
            --orange: #f59e0b;
            --orange-soft: rgba(245, 158, 11, 0.16);
            --red: #ef4444;
            --red-soft: rgba(239, 68, 68, 0.16);
            --blue: #38bdf8;
            --blue-soft: rgba(56, 189, 248, 0.16);
            --border-soft: rgba(255, 255, 255, 0.06);
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(34, 197, 94, 0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.10), transparent 24%),
                linear-gradient(180deg, #0d1117 0%, var(--bg-main) 100%);
            color: var(--text-main);
        }

        .dashboard-shell {
            max-width: 560px;
            margin: 0 auto;
            padding: 24px 16px 32px;
        }

        .glass-panel {
            background: rgba(23, 28, 34, 0.88);
            border: 1px solid var(--border-soft);
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.30);
            backdrop-filter: blur(10px);
        }

        .brand-chip,
        .stat-card,
        .action-card,
        .list-card,
        .sale-item {
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }

        .brand-chip:hover,
        .stat-card:hover,
        .action-card:hover,
        .list-card:hover,
        .sale-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.18);
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 22px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-chip {
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.26), rgba(56, 189, 248, 0.24));
            border: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 1.2rem;
        }

        .eyebrow {
            font-size: 0.78rem;
            color: var(--text-muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .page-title {
            margin: 0;
            font-size: 1.65rem;
            font-weight: 800;
        }

        .mini-pill {
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            border-radius: 24px;
            padding: 18px;
            border: 1px solid transparent;
            overflow: hidden;
            position: relative;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            inset: auto -20px -24px auto;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            opacity: 0.16;
            background: currentColor;
            filter: blur(12px);
        }

        .stat-card.sales {
            background: linear-gradient(180deg, rgba(34, 197, 94, 0.18), rgba(34, 197, 94, 0.09));
            border-color: rgba(34, 197, 94, 0.22);
            color: var(--green);
        }

        .stat-card.orders {
            background: linear-gradient(180deg, rgba(56, 189, 248, 0.18), rgba(56, 189, 248, 0.09));
            border-color: rgba(56, 189, 248, 0.22);
            color: var(--blue);
        }

        .stat-card.products {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.18), rgba(245, 158, 11, 0.09));
            border-color: rgba(245, 158, 11, 0.22);
            color: var(--orange);
        }

        .stat-card.alerts {
            background: linear-gradient(180deg, rgba(239, 68, 68, 0.18), rgba(239, 68, 68, 0.09));
            border-color: rgba(239, 68, 68, 0.22);
            color: var(--red);
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            margin-bottom: 18px;
            background: rgba(255, 255, 255, 0.10);
            color: #fff;
        }

        .stat-value {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .stat-label {
            margin-top: 6px;
            font-size: 0.86rem;
            color: var(--text-muted);
        }

        .action-card {
            display: block;
            text-decoration: none;
            color: #07130b;
            background: linear-gradient(135deg, #22c55e, #86efac);
            border-radius: 22px;
            padding: 18px 20px;
            font-weight: 800;
            margin-bottom: 22px;
            box-shadow: 0 16px 28px rgba(34, 197, 94, 0.22);
        }

        .action-card:hover {
            color: #07130b;
        }

        .action-card .action-subtext {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            opacity: 0.72;
            margin-top: 4px;
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-title h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
        }

        .section-title span {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .list-stack {
            display: grid;
            gap: 12px;
        }

        .list-card,
        .sale-item {
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

        .item-name {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .item-meta {
            margin-top: 4px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .stock-low {
            background: var(--red-soft);
            color: #ffb4b4;
        }

        .stock-ok {
            background: var(--green-soft);
            color: #a7f3c0;
        }

        .sales-summary {
            margin-top: 24px;
        }

        .sale-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .sale-avatar {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(56, 189, 248, 0.12);
            color: #7dd3fc;
            flex-shrink: 0;
        }

        .sale-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .sale-name {
            margin: 0;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .sale-time {
            margin-top: 4px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .sale-amount {
            font-weight: 800;
            color: #86efac;
            white-space: nowrap;
        }

        .empty-state {
            padding: 18px;
            text-align: center;
            border-radius: 20px;
            background: var(--bg-card);
            border: 1px dashed var(--border-soft);
            color: var(--text-muted);
        }

        @media (max-width: 420px) {
            .dashboard-shell {
                padding: 18px 12px 28px;
            }

            .stats-grid {
                gap: 12px;
            }

            .stat-card {
                padding: 16px;
            }

            .page-title {
                font-size: 1.45rem;
            }

            .mini-pill {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="dashboard-shell">
        <section class="glass-panel p-3 p-sm-4">
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
                <div class="mini-pill">
                    <i class="fa-solid fa-chart-line me-1"></i> Senegal
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card sales">
                    <div class="stat-icon">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <p class="stat-value"><?= number_format((float) ($daily_sales ?? 0), 0, ',', ' ') ?> FCFA</p>
                    <div class="stat-label">Chiffre d'affaires du jour</div>
                </article>

                <article class="stat-card orders">
                    <div class="stat-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <p class="stat-value"><?= $total_sales ?? 0 ?></p>
                    <div class="stat-label">Nombre de ventes</div>
                </article>

                <article class="stat-card products">
                    <div class="stat-icon">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <p class="stat-value"><?= $total_products ?? 0 ?></p>
                    <div class="stat-label">Produits en stock</div>
                </article>

                <article class="stat-card alerts">
                    <div class="stat-icon">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <p class="stat-value"><?= $low_stock ?? 0 ?></p>
                    <div class="stat-label">Produits en alerte</div>
                </article>
            </div>

            <a href="#" class="action-card">
                <i class="fa-solid fa-plus me-2"></i> Nouvelle vente rapide
                <span class="action-subtext">Acces direct a l'enregistrement d'une vente</span>
            </a>

            <section>
                <div class="section-title">
                    <h2><i class="fa-solid fa-box-open me-2 text-warning"></i>Liste produits</h2>
                    <span><?= is_array($products ?? null) ? count($products) : 0 ?> articles</span>
                </div>

                <div class="list-stack">
                    <?php if (! empty($products)): ?>
                        <?php foreach($products as $p): ?>
                            <?php $quantity = (int) ($p['quantite'] ?? 0); ?>
                            <article class="list-card">
                                <div>
                                    <p class="item-name"><?= esc($p['nom'] ?? 'Produit') ?></p>
                                    <div class="item-meta">
                                        Quantite disponible : <?= $quantity ?>
                                    </div>
                                </div>

                                <?php if ($quantity < 5): ?>
                                    <span class="stock-badge stock-low">
                                        <i class="fa-solid fa-arrow-trend-down"></i> Stock faible
                                    </span>
                                <?php else: ?>
                                    <span class="stock-badge stock-ok">
                                        <i class="fa-solid fa-circle-check"></i> Disponible
                                    </span>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-box-open mb-2"></i><br>
                            Aucun produit disponible pour le moment.
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="sales-summary">
                <div class="section-title">
                    <h2><i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>Dernieres ventes</h2>
                    <span>Aujourd'hui</span>
                </div>

                <div class="list-stack">
                    <?php foreach($recentSales as $sale): ?>
                        <article class="sale-item">
                            <div class="sale-left">
                                <div class="sale-avatar">
                                    <i class="fa-solid fa-bag-shopping"></i>
                                </div>
                                <div>
                                    <p class="sale-name"><?= esc($sale['client'] ?? 'Client') ?></p>
                                    <div class="sale-time">
                                        Vente enregistree a <?= esc($sale['heure'] ?? '--:--') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="sale-amount">
                                <?= number_format((float) ($sale['montant'] ?? 0), 0, ',', ' ') ?> FCFA
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </section>
    </main>
</body>
</html>
