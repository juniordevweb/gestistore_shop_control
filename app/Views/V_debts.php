<?= $this->extend('V_layout') ?>
<?= $this->section('content') ?>

<style>

:root{
    --bg-main:#101418;
    --bg-card:#1d242c;
    --text-main:#f5f7fa;
    --text-muted:#98a2b3;
    --green:#22c55e;
    --red:#ef4444;
    --orange:#f59e0b;
    --border-soft:rgba(255,255,255,.06);
}

body{
    background:
        radial-gradient(circle at top left, rgba(34,197,94,.10), transparent 28%),
        linear-gradient(180deg, #0d1117 0%, var(--bg-main) 100%);
}

/* HEADER */
.debt-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:20px;
}

.page-title{
    font-size:1.5rem;
    font-weight:800;
    color:#fff;
    margin:0;
}

.page-sub{
    font-size:.82rem;
    color:var(--text-muted);
    margin-top:4px;
}

/* STATS */
.stats-card{
    background:linear-gradient(135deg, rgba(239,68,68,.15), rgba(239,68,68,.08));
    border:1px solid rgba(255,255,255,.06);
    border-radius:24px;
    padding:18px;
    margin-bottom:22px;
}

.stats-value{
    font-size:1.6rem;
    font-weight:800;
    color:#fff;
}

.stats-label{
    color:var(--text-muted);
    font-size:.82rem;
}

/* DEBT CARD */
.debt-card{
    background:var(--bg-card);
    border:1px solid var(--border-soft);
    border-radius:22px;
    padding:16px;
    margin-bottom:14px;
}

.debt-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
}

.debt-user{
    display:flex;
    align-items:center;
    gap:12px;
}

.debt-avatar{
    width:48px;
    height:48px;
    border-radius:16px;
    background:rgba(245,158,11,.15);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fcd34d;
    font-size:1rem;
}

.debt-name{
    font-size:1rem;
    font-weight:800;
    color:#fff;
    margin:0;
}

.debt-type{
    color:var(--text-muted);
    font-size:.8rem;
    margin-top:4px;
}

.debt-amount{
    color:#fff;
    font-size:1.1rem;
    font-weight:800;
}

.badge-dette{
    display:inline-block;
    margin-top:10px;
    padding:7px 12px;
    border-radius:999px;
    background:rgba(239,68,68,.15);
    color:#ffb4b4;
    font-size:.75rem;
    font-weight:700;
}

/* ACTIONS */
.debt-actions{
    margin-top:16px;
    display:flex;
    justify-content:flex-end;
}

.delete-btn{
    width:44px;
    height:44px;
    border:none;
    border-radius:14px;
    background:rgba(239,68,68,.15);
    color:#ffb4b4;
    transition:.2s ease;
}

.delete-btn:active{
    transform:scale(.95);
}

/* EMPTY */
.empty-box{
    background:var(--bg-card);
    border:1px solid var(--border-soft);
    border-radius:24px;
    padding:30px 20px;
    text-align:center;
}

.empty-box i{
    font-size:2rem;
    color:#475569;
    margin-bottom:12px;
}

.empty-box h5{
    color:#fff;
    font-weight:800;
}

.empty-box p{
    color:var(--text-muted);
    font-size:.85rem;
    margin:0;
}

</style>

<div class="container py-3">

    <!-- HEADER -->
    <div class="debt-header">

        <div>
            <h1 class="page-title">
                <i class="fa-solid fa-wallet text-danger me-2"></i>
                Dettes
            </h1>

            <div class="page-sub">
                Liste des clients endettés
            </div>
        </div>

    </div>

    <!-- TOTAL DETTES -->
    <div class="stats-card">

        <div class="stats-value">
            <?=
                number_format(
                    array_sum(array_column($active_debts ?? [], 'montant')),
                    0,
                    ',',
                    ' '
                )
            ?> FCFA
        </div>

        <div class="stats-label">
            Montant total des dettes
        </div>

    </div>

    <!-- LISTE -->
    <?php if(!empty($active_debts)): ?>

        <?php foreach($active_debts as $debt): ?>

            <div class="debt-card">

                <div class="debt-top">

                    <div class="debt-user">

                        <div class="debt-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>

                            <p class="debt-name">
                                <?= esc($debt['client']) ?>
                            </p>

                            <div class="debt-type">
                                Dette client
                            </div>

                            <span class="badge-dette">
                                En attente
                            </span>

                        </div>

                    </div>

                    <div class="debt-amount">

                        <?= number_format($debt['montant'], 0, ',', ' ') ?>
                        FCFA

                    </div>

                </div>

                <!-- ACTION -->
                <div class="debt-actions">

                    <a
                        href="<?= base_url('debts/delete/'.$debt['client']) ?>"
                        class="delete-btn d-flex align-items-center justify-content-center"
                        onclick="return confirm('Supprimer cette dette ?')"
                    >
                        <i class="fa-solid fa-trash"></i>
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="empty-box">

            <i class="fa-solid fa-circle-check"></i>

            <h5>Aucune dette</h5>

            <p>
                Aucun client endetté pour le moment
            </p>

        </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>