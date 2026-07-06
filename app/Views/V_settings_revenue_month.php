<!DOCTYPE html>
<html lang="fr">
<head>
    <?= view('pwa_head') ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport mensuel - GestiStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #101418;
            --bg-card: #1d242c;
            --bg-soft: rgba(255,255,255,.05);
            --border-soft: rgba(255,255,255,.08);
            --text-main: #f5f7fa;
            --text-muted: #98a2b3;
            --green: #22c55e;
            --orange: #f59e0b;
            --blue: #38bdf8;
            --red: #ef4444;
            --nav-height: 72px;
        }

        * { box-sizing: border-box; }

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
            max-width: 900px;
            margin: 0 auto;
        }

        .glass-panel {
            background: rgba(15, 23, 42, .92);
            border: 1px solid var(--border-soft);
            border-radius: 24px;
            padding: 18px;
            margin-bottom: 18px;
            backdrop-filter: blur(14px);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: #0f172a;
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 16px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: .82rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .stat-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: #fff;
        }

        .btn-primary {
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #22c55e, #4ade80);
            color: #031b0c;
        }

        .btn-secondary {
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            color: #fff;
            background: rgba(255,255,255,.04);
        }

        .table {
            color: var(--text-main);
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            margin-bottom: 0;
        }

        .table thead th {
            background: rgba(255,255,255,.04);
            border-bottom: 1px solid var(--border-soft);
            color: var(--text-muted);
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 12px 14px;
        }

        .table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,.05);
        }

        .table tbody td {
            padding: 14px;
            font-size: .96rem;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(255,255,255,.02);
        }

        .total-row {
            background: rgba(34, 197, 94, .08);
            font-weight: 700;
            color: #fff;
        }

        .text-success { color: var(--green); }
        .text-warning { color: var(--orange); }
        .text-danger { color: var(--red); }

        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-chart-column text-warning"></i>
            Rapport mensuel
        </div>
        <p class="text-muted mb-0">Vérifiez le chiffre d'affaires et le bénéfice jour par jour pour <?= esc($month_label) ?>.</p>
    </div>

    <div class="glass-panel">
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">Chiffre d'affaires total</div>
                <div class="stat-value"><?= number_format((float) ($total_revenue ?? 0), 0, ',', ' ') ?> FCFA</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Bénéfice total</div>
                <div class="stat-value"><?= number_format((float) ($total_profit ?? 0), 0, ',', ' ') ?> FCFA</div>
            </div>
        </div>
        <a href="<?= base_url('settings') ?>" class="btn btn-secondary w-100">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour aux paramètres
        </a>
    </div>

    <div class="glass-panel">
        <div class="section-title">
            <i class="fa-solid fa-calendar-days text-success"></i>
            Détail journalier
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-end">Chiffre d'affaires</th>
                        <th class="text-end">Bénéfice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily_data as $day): ?>
                        <tr>
                            <td><?= esc($day['label']) ?></td>
                            <td class="text-end"><?= number_format((float) $day['revenue'], 0, ',', ' ') ?> FCFA</td>
                            <td class="text-end"><?= number_format((float) $day['profit'], 0, ',', ' ') ?> FCFA</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td>Total <?= esc($month_label) ?></td>
                        <td class="text-end"><?= number_format((float) ($total_revenue ?? 0), 0, ',', ' ') ?> FCFA</td>
                        <td class="text-end"><?= number_format((float) ($total_profit ?? 0), 0, ',', ' ') ?> FCFA</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= view('pwa_register') ?>
</body>
</html>
