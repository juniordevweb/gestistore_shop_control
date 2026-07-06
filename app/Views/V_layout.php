<?php
$path = trim(service('uri')->getPath(), '/');

$navItems = [
    ['href' => base_url('/'), 'label' => 'Accueil', 'icon' => 'fa-border-all', 'active' => $path === ''],
    ['href' => base_url('/stock'), 'label' => 'Stock', 'icon' => 'fa-cube', 'active' => $path === 'stock' || str_starts_with($path, 'stock/')],
    ['href' => base_url('sales/create'), 'label' => 'Ventes', 'icon' => 'fa-receipt', 'active' => $path === 'sales/create' || str_starts_with($path, 'sales/')],
    ['href' => base_url('/debts'), 'label' => 'Dettes', 'icon' => 'fa-users', 'active' => $path === 'debts' || str_starts_with($path, 'debts/')],
    ['href' => base_url('settings'), 'label' => 'Reglages', 'icon' => 'fa-gear', 'active' => $path === 'settings' || str_starts_with($path, 'settings/')],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?= view('pwa_head') ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestiStore</title>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --nav-height: 72px;
            --nav-bg: rgba(17, 24, 39, 0.95);
            --nav-border: rgba(255, 255, 255, .06);
            --nav-muted: #94a3b8;
            --nav-active: #22c55e;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            padding-bottom: calc(var(--nav-height) + 12px);
            overflow-x: hidden;
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
            background: var(--nav-bg);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-top: 1px solid var(--nav-border);
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
            color: var(--nav-muted);
            transition: .25s ease;
        }

        .bn-item span {
            margin-top: 4px;
            font-size: clamp(.55rem, 2.5vw, .68rem);
            font-weight: 700;
            color: var(--nav-muted);
            transition: .25s ease;
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
            color: var(--nav-active);
        }

        .bn-dot {
            position: absolute;
            top: 6px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--nav-active);
            opacity: 0;
            transition: .25s ease;
        }

        .bn-item.active .bn-dot {
            opacity: 1;
        }

        .bn-item:active {
            transform: scale(.96);
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
<body class="bg-dark text-white">

<div class="container py-3">
    <?= $this->renderSection('content') ?>
</div>

<nav class="bottom-nav" aria-label="Navigation principale">
    <?php foreach ($navItems as $item): ?>
        <a href="<?= $item['href'] ?>" class="bn-item<?= $item['active'] ? ' active' : '' ?>"<?= $item['active'] ? ' aria-current="page"' : '' ?>>
            <i class="fa-solid <?= esc($item['icon']) ?>" aria-hidden="true"></i>
            <div class="bn-dot"></div>
            <span><?= esc($item['label']) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<?= $this->renderSection('scripts') ?>

<?= view('pwa_register') ?>
</body>
</html>
