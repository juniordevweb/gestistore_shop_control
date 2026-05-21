<?= $this->extend('V_layout') ?>
<?= $this->section('content') ?>

<style>
/* on réutilise le même style dashboard */
body {
    background: linear-gradient(180deg, #0d1117 0%, #101418 100%);
}

/* HEADER MOBILE */
.stock-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.stock-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: #fff;
}

.stock-sub {
    font-size: .8rem;
    color: #98a2b3;
}

/* FLOAT BUTTON */
.fab-btn {
    width: 52px;
    height: 52px;
    border-radius: 18px;
    background: linear-gradient(135deg, #22c55e, #86efac);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #07130b;
    font-weight: bold;
    box-shadow: 0 10px 25px rgba(34,197,94,.25);
}

/* STATS CARDS (comme dashboard) */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 18px;
}

.stat-card {
    background: #1d242c;
    border-radius: 22px;
    padding: 14px;
    border: 1px solid rgba(255,255,255,.06);
}

.stat-value {
    font-size: 1.2rem;
    font-weight: 800;
    color: #fff;
}

.stat-label {
    font-size: .75rem;
    color: #98a2b3;
}

/* SEARCH */
.search-box input {
    background: #1d242c;
    border: none;
    border-radius: 16px;
    padding: 14px 14px 14px 40px;
    color: #fff;
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 14px;
    color: #98a2b3;
}

/* PRODUCT CARD */
.product-card {
    background: #1d242c;
    border-radius: 20px;
    padding: 14px;
    border: 1px solid rgba(255,255,255,.06);
    margin-bottom: 12px;
}

.product-title {
    font-weight: 800;
    color: #fff;
    font-size: 1rem;
}

.badge-soft {
    font-size: .7rem;
    padding: 6px 10px;
    border-radius: 999px;
}

.badge-danger {
    background: rgba(239,68,68,.15);
    color: #ffb4b4;
}

.badge-success {
    background: rgba(34,197,94,.15);
    color: #a7f3c0;
}

.price {
    color: #22c55e;
    font-weight: 800;
}

.actions a {
    flex: 1;
    border-radius: 14px;
    padding: 10px;
    font-size: .8rem;
    text-align: center;
}
</style>

<div class="container py-3">

    <!-- HEADER -->
    <div class="stock-header">
        <div>
            <div class="stock-title">
                <i class="fa-solid fa-boxes-stacked text-success me-2"></i>
                Stock
            </div>
            <div class="stock-sub">Gestion des produits</div>
        </div>

        <button class="fab-btn"
                data-bs-toggle="modal"
                data-bs-target="#addProductModal">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>

    <!-- STATS -->
    <div class="stats-grid">

        <div class="stat-card">
            <div class="stock-sub">Produits</div>
            <div class="stat-value"><?= count($products ?? []) ?></div>
        </div>

        <div class="stat-card">
            <div class="stock-sub">Stock faible</div>
            <div class="stat-value text-danger"><?= $low_stock ?? 0 ?></div>
        </div>

    </div>

    <!-- SEARCH -->
    <div class="position-relative search-box mb-3">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input type="text"
               id="searchProduct"
               class="form-control"
               placeholder="Rechercher un produit...">

    </div>

    <!-- LIST -->
    <div id="productContainer">

        <?php foreach ($products ?? [] as $p): ?>

            <div class="product-card product-item">

                <div class="d-flex justify-content-between align-items-start">

                    <div>
                        <div class="product-title"><?= esc($p['nom']) ?></div>

                        <?php if ((float) ($p['quantite_display'] ?? 0) < 5): ?>
                            <span class="badge-soft badge-danger">Stock faible</span>
                        <?php else: ?>
                            <span class="badge-soft badge-success">Disponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="text-end">
                        <div class="price">
                            <?= number_format((float) ($p['prix_vente_display'] ?? 0), 0, '.', ' ') ?> FCFA / <?= esc($p['display_unit_label'] ?? 'g') ?>
                        </div>
                        <small class="text-secondary">vente</small>
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-3 text-center">

                    <div>
                        <small class="text-secondary">Achat</small><br>
                        <span class="text-warning fw-bold">
                            <?= number_format((float) ($p['prix_achat_display'] ?? 0), 0, '.', ' ') ?> / <?= esc($p['display_unit_label'] ?? 'g') ?>
                        </span>
                    </div>

                    <div>
                        <small class="text-secondary">Stock</small><br>
                        <span class="text-white fw-bold">
                            <?= number_format((float) ($p['quantite_display'] ?? 0), 3, '.', ' ') ?> <?= esc($p['display_unit_label'] ?? 'g') ?>
                        </span>
                    </div>

                    <div>
                        <small class="text-secondary">Valeur</small><br>
                        <span class="text-info fw-bold">
                            <?= number_format((float) ($p['stock_value'] ?? 0), 0, '.', ' ') ?>
                        </span>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-3 actions">

                    <a href="<?= base_url('stock/edit/'.$p['id']) ?>"
                       class="btn btn-warning">
                        Modifier
                    </a>

                    <a href="<?= base_url('stock/delete/'.$p['id']) ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Supprimer ?')">
                        Supprimer
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un produit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('/stock/store') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prix_achat" class="form-label">Prix d'achat</label>
                        <input type="number" class="form-control" id="prix_achat" name="prix_achat" min="0" step="0.001" required>
                    </div>
                    <div class="mb-3">
                        <label for="prix_vente" class="form-label">Prix de vente</label>
                        <input type="number" class="form-control" id="prix_vente" name="prix_vente" min="0" step="0.001" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantite" class="form-label">Quantité</label>
                        <input type="number" class="form-control" id="quantite" name="quantite" min="0" step="0.001" required>
                    </div>
                    <div class="mb-3">
                        <label for="unite_affichage" class="form-label">UnitÃ© de saisie / affichage</label>
                        <select class="form-select" id="unite_affichage" name="unite_affichage" required>
                            <option value="kg">kg</option>
                            <option value="g">g</option>
                            <option value="litre">litre</option>
                            <option value="ml">ml</option>
                        </select>
                    </div>
                </div>  
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Ajouter</button>  
                </div>
            </form>
        </div>
    </div>
</div>



<!-- SEARCH JS -->
<script>
document.getElementById('searchProduct').addEventListener('input', function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(el => {
        el.style.display = el.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>

<?= $this->endSection() ?>
