<?= $this->extend('V_layout') ?>
<?= $this->section('content') ?>

<style>
body {
    background: linear-gradient(180deg, #0d1117 0%, #101418 100%);
}

.edit-shell {
    max-width: 620px;
    margin: 0 auto;
}

.edit-card {
    background: #1d242c;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 24px;
    padding: 18px;
    box-shadow: 0 16px 36px rgba(0,0,0,.20);
}

.edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.edit-title {
    margin: 0;
    color: #fff;
    font-size: 1.35rem;
    font-weight: 800;
}

.edit-sub {
    color: #98a2b3;
    font-size: .82rem;
    margin-top: 4px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 14px;
    text-decoration: none;
    background: rgba(255,255,255,.05);
    color: #fff;
}

.form-label {
    color: #cbd5e1;
    font-weight: 700;
}

.form-control {
    min-height: 52px;
    border-radius: 16px;
    background: #0f172a;
    border: 1px solid rgba(255,255,255,.08);
    color: #f8fafc;
}

.form-control:focus {
    background: #0f172a;
    color: #f8fafc;
    border-color: #22c55e;
    box-shadow: 0 0 0 .2rem rgba(34,197,94,.18);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.form-actions .btn {
    flex: 1 1 220px;
    min-height: 52px;
    border-radius: 16px;
    font-weight: 700;
}

.btn-save {
    background: linear-gradient(135deg, #22c55e, #4ade80);
    border: none;
    color: #052e16;
}

.btn-cancel {
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    color: #e2e8f0;
}

@media (max-width: 576px) {
    .edit-header {
        align-items: flex-start;
    }

    .edit-title {
        font-size: 1.2rem;
    }

    .form-actions .btn {
        flex-basis: 100%;
    }
}
</style>

<div class="container py-3">
    <div class="edit-shell">
        <div class="edit-card">
            <div class="edit-header">
                <div>
                    <h1 class="edit-title">
                        <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                        Modifier le produit
                    </h1>
                    <div class="edit-sub">Mettez a jour les informations du produit</div>
                </div>
                <a href="<?= base_url('/stock') ?>" class="back-link" aria-label="Retour au stock">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>

            <form action="<?= base_url('stock/update/' . $product['id']) ?>" method="post">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du produit</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= esc($product['nom']) ?>" required>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="prix_achat" class="form-label">Prix d'achat</label>
                        <input type="number" class="form-control" id="prix_achat" name="prix_achat" min="0" step="0.001" value="<?= esc($product['prix_achat_display']) ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="prix_vente" class="form-label">Prix de vente</label>
                        <input type="number" class="form-control" id="prix_vente" name="prix_vente" min="0" step="0.001" value="<?= esc($product['prix_vente_display']) ?>" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="quantite" class="form-label">Quantite</label>
                    <input type="number" class="form-control" id="quantite" name="quantite" min="0" step="0.001" value="<?= esc($product['quantite_display']) ?>" required>
                </div>

                <div class="mt-3">
                    <label for="unite_affichage" class="form-label">UnitÃ© de saisie / affichage</label>
                    <select class="form-control" id="unite_affichage" name="unite_affichage" required>
                        <option value="kg" <?= ($product['unite_affichage'] ?? '') === 'kg' ? 'selected' : '' ?>>kg</option>
                        <option value="g" <?= ($product['unite_affichage'] ?? '') === 'g' ? 'selected' : '' ?>>g</option>
                        <option value="litre" <?= ($product['unite_affichage'] ?? '') === 'litre' ? 'selected' : '' ?>>litre</option>
                        <option value="ml" <?= ($product['unite_affichage'] ?? '') === 'ml' ? 'selected' : '' ?>>ml</option>
                    </select>
                </div>

                <div class="form-actions">
                    <a href="<?= base_url('/stock') ?>" class="btn btn-cancel">Annuler</a>
                    <button type="submit" class="btn btn-save">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
