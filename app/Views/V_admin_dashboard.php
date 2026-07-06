<?php
$summary = $summary ?? [];
$shopsSummary = $shops_summary ?? [];
$recentShops = $recent_shops ?? [];
$recentSales = $recent_sales ?? [];
$monthlyTrend = $monthly_trend ?? [];
$pendingUsers = $pending_users ?? [];

$trendSlice = array_slice($monthlyTrend, -7);
$maxTrendRevenue = max(1, (int) max(array_map(fn(array $row) => (float) ($row['revenue'] ?? 0), $trendSlice) ?: [1]));
$topShops = array_slice($shopsSummary, 0, 6);
$bestShopRevenue = max(1, (int) max(array_map(fn(array $row) => (float) ($row['revenue'] ?? 0), $topShops) ?: [1]));

$statusLabel = static function (?string $status): string {
    return match ($status) {
        'approved' => 'Active',
        'pending' => 'En attente',
        'rejected' => 'Refusee',
        default => 'Inconnue',
    };
};

$statusClass = static function (?string $status): string {
    return match ($status) {
        'approved' => 'good',
        'pending' => 'warn',
        'rejected' => 'bad',
        default => 'neutral',
    };
};

$paymentLabel = static function (?string $method): string {
    return match ($method) {
        'dette' => 'Dette',
        'card' => 'Carte',
        'mobile' => 'Mobile',
        default => 'Cash',
    };
};
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?= view('pwa_head') ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord admin - GestiStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root{
            --bg-main:#0d1117;
            --bg-panel:rgba(17,24,39,.92);
            --bg-card:#171c22;
            --bg-soft:rgba(255,255,255,.05);
            --border-soft:rgba(255,255,255,.08);
            --text-main:#f5f7fa;
            --text-muted:#94a3b8;
            --green:#22c55e;
            --green-soft:rgba(34,197,94,.16);
            --blue:#38bdf8;
            --blue-soft:rgba(56,189,248,.16);
            --orange:#f59e0b;
            --orange-soft:rgba(245,158,11,.16);
            --red:#ef4444;
            --red-soft:rgba(239,68,68,.16);
            --nav-h:74px;
        }

        *{box-sizing:border-box}

        body{
            margin:0;
            min-height:100vh;
            font-family:'Manrope',sans-serif;
            color:var(--text-main);
            background:
                radial-gradient(circle at top left, rgba(34,197,94,.14), transparent 26%),
                radial-gradient(circle at top right, rgba(56,189,248,.12), transparent 24%),
                radial-gradient(circle at 75% 15%, rgba(245,158,11,.12), transparent 18%),
                linear-gradient(180deg,#0a0d12 0%, var(--bg-main) 100%);
            padding:18px 14px calc(var(--nav-h) + 18px);
            overflow-x:hidden;
        }

        .page-shell{
            max-width:1240px;
            margin:0 auto;
        }

        .glass-panel{
            background:var(--bg-panel);
            border:1px solid var(--border-soft);
            border-radius:28px;
            padding:18px;
            backdrop-filter:blur(16px);
            -webkit-backdrop-filter:blur(16px);
            box-shadow:0 18px 40px rgba(0,0,0,.24);
        }

        .hero{
            display:grid;
            grid-template-columns:1.4fr .9fr;
            gap:16px;
            margin-bottom:18px;
        }

        .hero-main{
            position:relative;
            overflow:hidden;
            background:
                linear-gradient(135deg, rgba(34,197,94,.22), rgba(56,189,248,.12)),
                rgba(17,24,39,.92);
        }

        .hero-main::after{
            content:'';
            position:absolute;
            inset:auto -10% -40% auto;
            width:260px;
            height:260px;
            border-radius:50%;
            background:radial-gradient(circle, rgba(255,255,255,.12), transparent 68%);
            pointer-events:none;
        }

        .eyebrow{
            color:var(--text-muted);
            text-transform:uppercase;
            letter-spacing:.14em;
            font-size:.74rem;
            font-weight:700;
            margin-bottom:10px;
        }

        .hero-title{
            font-size:2rem;
            font-weight:800;
            line-height:1.05;
            margin:0 0 12px;
        }

        .hero-copy{
            color:#cbd5e1;
            max-width:64ch;
            margin:0 0 18px;
        }

        .hero-actions{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .btn-ghost,
        .btn-accent{
            display:inline-flex;
            align-items:center;
            gap:10px;
            border-radius:14px;
            padding:12px 16px;
            text-decoration:none;
            font-weight:800;
            transition:.2s ease;
        }

        .btn-accent{
            color:#04140b;
            background:linear-gradient(135deg, #22c55e, #86efac);
        }

        .btn-ghost{
            color:#fff;
            background:rgba(255,255,255,.04);
            border:1px solid var(--border-soft);
        }

        .btn-accent:active,
        .btn-ghost:active{
            transform:scale(.98);
        }

        .hero-side{
            display:grid;
            gap:14px;
        }

        .mini-card{
            background:rgba(15,23,42,.9);
            border:1px solid var(--border-soft);
            border-radius:22px;
            padding:16px;
        }

        .mini-label{
            color:var(--text-muted);
            font-size:.78rem;
            text-transform:uppercase;
            letter-spacing:.08em;
            margin-bottom:8px;
        }

        .mini-value{
            font-size:1.55rem;
            font-weight:800;
            margin:0;
        }

        .mini-sub{
            color:#cbd5e1;
            font-size:.85rem;
            margin-top:6px;
        }

        .stats-grid{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:14px;
            margin-bottom:18px;
        }

        .stat-card{
            position:relative;
            overflow:hidden;
            border-radius:24px;
            padding:18px;
            border:1px solid var(--border-soft);
            background:rgba(23,28,34,.92);
            min-height:150px;
        }

        .stat-card::after{
            content:'';
            position:absolute;
            right:-18px;
            top:-18px;
            width:96px;
            height:96px;
            border-radius:50%;
            background:radial-gradient(circle, rgba(255,255,255,.14), transparent 68%);
        }

        .stat-card.revenue{ background:linear-gradient(180deg, rgba(34,197,94,.18), rgba(23,28,34,.96)); }
        .stat-card.profit{ background:linear-gradient(180deg, rgba(56,189,248,.18), rgba(23,28,34,.96)); }
        .stat-card.shops{ background:linear-gradient(180deg, rgba(245,158,11,.18), rgba(23,28,34,.96)); }
        .stat-card.alerts{ background:linear-gradient(180deg, rgba(239,68,68,.18), rgba(23,28,34,.96)); }

        .stat-icon{
            width:46px;
            height:46px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:16px;
            background:rgba(255,255,255,.10);
            margin-bottom:16px;
        }

        .stat-value{
            font-size:1.5rem;
            font-weight:800;
            margin:0;
        }

        .stat-label{
            color:var(--text-muted);
            margin-top:6px;
            font-size:.85rem;
        }

        .section-card{
            margin-bottom:18px;
        }

        .section-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            margin-bottom:14px;
        }

        .section-title{
            margin:0;
            font-size:1.1rem;
            font-weight:800;
        }

        .section-sub{
            margin:6px 0 0;
            color:var(--text-muted);
            font-size:.86rem;
        }

        .badge-soft{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:8px 12px;
            border-radius:999px;
            background:rgba(255,255,255,.05);
            color:#e2e8f0;
            font-size:.78rem;
            font-weight:700;
            border:1px solid var(--border-soft);
            white-space:nowrap;
        }

        .table-wrap{
            background:rgba(15,23,42,.88);
            border:1px solid var(--border-soft);
            border-radius:22px;
            overflow:hidden;
        }

        .table{
            color:var(--text-main);
            margin:0;
        }

        .table thead th{
            background:rgba(255,255,255,.04);
            color:var(--text-muted);
            border-bottom:1px solid var(--border-soft);
            font-size:.78rem;
            text-transform:uppercase;
            letter-spacing:.06em;
            padding:14px 14px;
            white-space:nowrap;
        }

        .table tbody td{
            padding:14px;
            vertical-align:middle;
            border-color:rgba(255,255,255,.05);
        }

        .shop-name{
            font-weight:800;
            margin:0;
        }

        .shop-meta{
            color:var(--text-muted);
            font-size:.78rem;
            margin-top:4px;
            overflow-wrap:anywhere;
        }

        .status-pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:7px 11px;
            border-radius:999px;
            font-size:.75rem;
            font-weight:700;
            white-space:nowrap;
        }

        .status-pill.good{ background:rgba(34,197,94,.15); color:#86efac; }
        .status-pill.warn{ background:rgba(245,158,11,.15); color:#fcd34d; }
        .status-pill.bad{ background:rgba(239,68,68,.15); color:#fca5a5; }
        .status-pill.neutral{ background:rgba(148,163,184,.15); color:#cbd5e1; }

        .trend-list{
            display:grid;
            gap:10px;
        }

        .trend-row{
            background:rgba(15,23,42,.86);
            border:1px solid var(--border-soft);
            border-radius:18px;
            padding:12px 14px;
        }

        .trend-head{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            margin-bottom:8px;
            font-size:.88rem;
        }

        .trend-track{
            width:100%;
            height:10px;
            border-radius:999px;
            background:rgba(255,255,255,.06);
            overflow:hidden;
        }

        .trend-fill{
            height:100%;
            border-radius:999px;
            background:linear-gradient(90deg, #22c55e, #38bdf8);
        }

        .dual-grid{
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:14px;
        }

        .list-stack{
            display:grid;
            gap:12px;
        }

        .stack-item{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            background:rgba(15,23,42,.88);
            border:1px solid var(--border-soft);
            border-radius:20px;
            padding:14px 15px;
        }

        .stack-left{
            display:flex;
            align-items:flex-start;
            gap:12px;
            min-width:0;
        }

        .stack-icon{
            width:42px;
            height:42px;
            border-radius:14px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
            background:rgba(56,189,248,.12);
            color:#7dd3fc;
        }

        .stack-title{
            margin:0;
            font-weight:800;
        }

        .stack-meta{
            color:var(--text-muted);
            font-size:.8rem;
            margin-top:4px;
            overflow-wrap:anywhere;
        }

        .stack-right{
            text-align:right;
            flex-shrink:0;
        }

        .stack-value{
            font-weight:800;
            margin:0;
        }

        .stack-sub{
            color:var(--text-muted);
            font-size:.75rem;
            margin-top:4px;
        }

        .bottom-nav{
            position:fixed;
            left:0;
            right:0;
            bottom:0;
            height:var(--nav-h);
            display:grid;
            grid-template-columns:repeat(5, minmax(0, 1fr));
            align-items:center;
            padding:8px 10px env(safe-area-inset-bottom);
            background:rgba(17,24,39,.96);
            backdrop-filter:blur(18px);
            -webkit-backdrop-filter:blur(18px);
            border-top:1px solid var(--border-soft);
            z-index:9999;
        }

        .bn-item{
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            gap:4px;
            min-width:0;
            border-radius:18px;
            text-decoration:none;
            padding:8px 4px;
            position:relative;
        }

        .bn-item i{
            font-size:1.15rem;
            color:#94a3b8;
        }

        .bn-item span{
            font-size:clamp(.55rem, 2.4vw, .7rem);
            font-weight:700;
            color:#94a3b8;
            text-align:center;
            white-space:normal;
            overflow-wrap:anywhere;
            line-height:1.1;
        }

        .bn-item.active{
            background:rgba(34,197,94,.12);
        }

        .bn-item.active i,
        .bn-item.active span{
            color:#22c55e;
        }

        .bn-dot{
            width:4px;
            height:4px;
            border-radius:50%;
            background:#22c55e;
            margin-top:-2px;
            opacity:0;
        }

        .bn-item.active .bn-dot{
            opacity:1;
        }

        @media (max-width: 1100px){
            .hero,
            .stats-grid,
            .dual-grid{
                grid-template-columns:1fr;
            }
        }

        @media (max-width: 576px){
            body{
                padding-left:10px;
                padding-right:10px;
            }

            .glass-panel{
                padding:14px;
                border-radius:22px;
            }

            .hero-title{
                font-size:1.65rem;
            }

            .section-head{
                flex-direction:column;
            }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <section class="hero">
        <div class="glass-panel hero-main">
            <div class="eyebrow">Tableau de bord administrateur</div>
            <h1 class="hero-title">Pilotage global de toutes les boutiques</h1>
            <p class="hero-copy">
                Vue consolidée des chiffres réels: boutiques, ventes, dettes, stocks et performance par point de vente.
            </p>
            <div class="hero-actions">
                <a href="<?= base_url('settings') ?>" class="btn-accent">
                    <i class="fa-solid fa-gear"></i>
                    Ouvrir les paramètres
                </a>
                <a href="<?= base_url('settings/revenue') ?>" class="btn-ghost">
                    <i class="fa-solid fa-chart-column"></i>
                    Rapport mensuel
                </a>
            </div>
        </div>

        <div class="hero-side">
            <div class="mini-card">
                <div class="mini-label">Connexion admin</div>
                <p class="mini-value"><?= esc($admin_email ?? session()->get('email') ?? 'Admin') ?></p>
                <div class="mini-sub">Acces global aux donnees de la plateforme</div>
            </div>
            <div class="mini-card">
                <div class="mini-label">Bilan du jour</div>
                <p class="mini-value"><?= number_format((float) ($summary['daily_revenue'] ?? 0), 0, ',', ' ') ?> FCFA</p>
                <div class="mini-sub">
                    Benefice: <?= number_format((float) ($summary['daily_profit'] ?? 0), 0, ',', ' ') ?> FCFA
                </div>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card shops">
            <div class="stat-icon"><i class="fa-solid fa-store"></i></div>
            <p class="stat-value"><?= (int) ($summary['total_shops'] ?? 0) ?></p>
            <div class="stat-label">Boutiques au total</div>
        </article>

        <article class="stat-card revenue">
            <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <p class="stat-value"><?= number_format((float) ($summary['total_revenue'] ?? 0), 0, ',', ' ') ?> FCFA</p>
            <div class="stat-label">Chiffre d'affaires cumule</div>
        </article>

        <article class="stat-card profit">
            <div class="stat-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
            <p class="stat-value"><?= number_format((float) ($summary['daily_profit'] ?? 0), 0, ',', ' ') ?> FCFA</p>
            <div class="stat-label">Benefice du jour</div>
        </article>

        <article class="stat-card alerts">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <p class="stat-value"><?= (int) ($summary['low_stock_products'] ?? 0) ?></p>
            <div class="stat-label">Produits en alerte stock</div>
        </article>

        <article class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
            <p class="stat-value"><?= (int) ($summary['total_sales'] ?? 0) ?></p>
            <div class="stat-label">Ventes enregistrees</div>
        </article>

        <article class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <p class="stat-value"><?= (int) ($summary['total_products'] ?? 0) ?></p>
            <div class="stat-label">Produits en base</div>
        </article>

        <article class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
            <p class="stat-value"><?= number_format((float) ($summary['total_debt'] ?? 0), 0, ',', ' ') ?> FCFA</p>
            <div class="stat-label">Dettes en attente</div>
        </article>

        <article class="stat-card">
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            <p class="stat-value"><?= (int) ($summary['approved_shops'] ?? 0) ?></p>
            <div class="stat-label">Boutiques actives</div>
        </article>
    </section>

    <section class="glass-panel section-card">
        <div class="section-head">
            <div>
                <h2 class="section-title">Performance des boutiques</h2>
                <p class="section-sub">Classement par chiffre d'affaires avec stock, benefice et dettes par boutique.</p>
            </div>
            <span class="badge-soft">
                <i class="fa-solid fa-filter"></i>
                <?= count($shopsSummary) ?> boutiques suivies
            </span>
        </div>

        <div class="table-wrap table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Boutique</th>
                        <th>Statut</th>
                        <th class="text-end">Ventes</th>
                        <th class="text-end">CA</th>
                        <th class="text-end">Benefice</th>
                        <th class="text-end">Dettes</th>
                        <th class="text-end">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (! empty($topShops)): ?>
                        <?php foreach ($topShops as $shop): ?>
                            <tr>
                                <td>
                                    <p class="shop-name"><?= esc($shop['shop_name'] ?? 'Boutique') ?></p>
                                    <div class="shop-meta">
                                        <?= esc($shop['email'] ?? 'Email inconnu') ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill <?= esc($statusClass($shop['account_status'] ?? null)) ?>">
                                        <i class="fa-solid fa-circle"></i>
                                        <?= esc($statusLabel($shop['account_status'] ?? null)) ?>
                                    </span>
                                </td>
                                <td class="text-end"><?= number_format((int) ($shop['sales_count'] ?? 0), 0, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format((float) ($shop['revenue'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                                <td class="text-end"><?= number_format((float) ($shop['profit'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                                <td class="text-end"><?= number_format((float) ($shop['debt'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                                <td class="text-end">
                                    <div><?= number_format((float) ($shop['stock_total'] ?? 0), 0, ',', ' ') ?></div>
                                    <div class="shop-meta"><?= (int) ($shop['low_stock_count'] ?? 0) ?> alertes</div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucune boutique trouvee.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dual-grid">
        <div class="glass-panel section-card">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Tendance du mois</h2>
                    <p class="section-sub">Evolution quotidienne du chiffre d'affaires sur les derniers jours visibles.</p>
                </div>
                <span class="badge-soft">
                    <i class="fa-solid fa-calendar-days"></i>
                    Mois en cours
                </span>
            </div>

            <div class="trend-list">
                <?php if (! empty($trendSlice)): ?>
                    <?php foreach ($trendSlice as $day): ?>
                        <?php $width = ((float) ($day['revenue'] ?? 0) / $maxTrendRevenue) * 100; ?>
                        <div class="trend-row">
                            <div class="trend-head">
                                <strong><?= esc($day['label'] ?? '') ?></strong>
                                <span><?= number_format((float) ($day['revenue'] ?? 0), 0, ',', ' ') ?> FCFA</span>
                            </div>
                            <div class="trend-track">
                                <div class="trend-fill" style="width: <?= max(4, min(100, $width)) ?>%"></div>
                            </div>
                            <div class="shop-meta mt-2">
                                Benefice: <?= number_format((float) ($day['profit'] ?? 0), 0, ',', ' ') ?> FCFA
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">Aucune donnee mensuelle disponible.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-panel section-card">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Dernieres boutiques</h2>
                    <p class="section-sub">Comptes recents et etats de validation.</p>
                </div>
                <span class="badge-soft">
                    <i class="fa-solid fa-user-clock"></i>
                    <?= count($recentShops) ?> recents
                </span>
            </div>

            <div class="list-stack">
                <?php if (! empty($recentShops)): ?>
                    <?php foreach ($recentShops as $shop): ?>
                        <div class="stack-item">
                            <div class="stack-left">
                                <div class="stack-icon">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <div style="min-width:0">
                                    <p class="stack-title"><?= esc($shop['shop_name'] ?? 'Boutique') ?></p>
                                    <div class="stack-meta">
                                        <?= esc($shop['email'] ?? 'Email inconnu') ?><br>
                                        Cree le <?= esc($shop['created_at'] ?? '--') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="stack-right">
                                <p class="stack-value"><?= esc($statusLabel($shop['account_status'] ?? null)) ?></p>
                                <div class="stack-sub"><?= esc($shop['shop_id'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">Aucune boutique recente.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="dual-grid">
        <div class="glass-panel section-card">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Dernieres ventes</h2>
                    <p class="section-sub">Flux transactionnel global de toutes les boutiques.</p>
                </div>
                <span class="badge-soft">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <?= count($recentSales) ?> ventes
                </span>
            </div>

            <div class="list-stack">
                <?php if (! empty($recentSales)): ?>
                    <?php foreach ($recentSales as $sale): ?>
                        <div class="stack-item">
                            <div class="stack-left">
                                <div class="stack-icon" style="background:rgba(34,197,94,.12);color:#86efac">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div style="min-width:0">
                                    <p class="stack-title"><?= esc($sale['shop_name'] ?? 'Boutique') ?></p>
                                    <div class="stack-meta">
                                        <?= esc($sale['client'] ?? 'Client') ?> • <?= esc($sale['heure'] ?? '--:--') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="stack-right">
                                <p class="stack-value"><?= number_format((float) ($sale['montant'] ?? 0), 0, ',', ' ') ?> FCFA</p>
                                <div class="stack-sub"><?= esc($paymentLabel($sale['payment_method'] ?? null)) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">Aucune vente recente.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-panel section-card">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Validation rapide</h2>
                    <p class="section-sub">Boutiques en attente de confirmation.</p>
                </div>
                <span class="badge-soft">
                    <i class="fa-solid fa-bell"></i>
                    <?= (int) ($summary['pending_shops'] ?? 0) ?> en attente
                </span>
            </div>

            <div class="list-stack">
                <?php if (! empty($pendingUsers)): ?>
                    <?php foreach (array_slice($pendingUsers, 0, 5) as $shop): ?>
                        <div class="stack-item">
                            <div class="stack-left">
                                <div class="stack-icon" style="background:rgba(245,158,11,.15);color:#fcd34d">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                                <div style="min-width:0">
                                    <p class="stack-title"><?= esc($shop['shop_name'] ?? 'Boutique') ?></p>
                                    <div class="stack-meta">
                                        <?= esc($shop['email'] ?? 'Email inconnu') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="stack-right">
                                <p class="stack-value">En attente</p>
                                <div class="stack-sub"><?= esc($shop['shop_id'] ?? '') ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted">Aucune boutique en attente.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<nav class="bottom-nav" aria-label="Navigation admin">
    <a href="<?= base_url('/') ?>" class="bn-item active" aria-current="page">
        <i class="fa-solid fa-chart-line"></i>
        <div class="bn-dot"></div>
        <span>Dashboard</span>
    </a>
    <a href="<?= base_url('settings') ?>" class="bn-item">
        <i class="fa-solid fa-gear"></i>
        <div class="bn-dot"></div>
        <span>Parametres</span>
    </a>
    <a href="<?= base_url('settings/revenue') ?>" class="bn-item">
        <i class="fa-solid fa-chart-column"></i>
        <div class="bn-dot"></div>
        <span>Revenue</span>
    </a>
    <a href="<?= base_url('settings') ?>" class="bn-item">
        <i class="fa-solid fa-users-gear"></i>
        <div class="bn-dot"></div>
        <span>Boutiques</span>
    </a>
    <a href="<?= base_url('logout') ?>" class="bn-item">
        <i class="fa-solid fa-right-from-bracket"></i>
        <div class="bn-dot"></div>
        <span>Sortie</span>
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= view('pwa_register') ?>
</body>
</html>
