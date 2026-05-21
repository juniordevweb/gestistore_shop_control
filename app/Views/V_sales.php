<?php echo $this->extend('V_layout'); ?>

<?php echo $this->section('content'); ?>

<?php
$formatQty = static function ($value): string {
    return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
};
?>

<style>
.sales-shell{max-width:1220px;margin:0 auto;padding:.5rem 0 1.5rem}
.sales-grid{display:grid;gap:1rem}
.sales-panel{background:rgba(29,36,44,.94);border:1px solid rgba(255,255,255,.06);border-radius:22px;box-shadow:0 16px 38px rgba(0,0,0,.24)}
.sales-panel-body{padding:1rem}
.sales-title{font-size:1.6rem;font-weight:800;color:#fff;margin:0 0 .25rem}
.sales-subtitle{color:#98a2b3;margin:0 0 1rem}
.search-input,.sales-input,.sales-select,.sales-number{background:#11161c;border:1px solid rgba(255,255,255,.08);color:#fff;border-radius:14px}
.search-input:focus,.sales-input:focus,.sales-select:focus,.sales-number:focus{background:#11161c;color:#fff;border-color:rgba(34,197,94,.45);box-shadow:0 0 0 .2rem rgba(34,197,94,.12)}
.products-list,.cart-list{display:grid;gap:1rem}
.product-card,.cart-card{background:#171c22;border:1px solid rgba(255,255,255,.05);border-radius:18px;padding:1rem}
.product-head,.cart-head{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start}
.product-name,.cart-name{font-size:1rem;font-weight:800;color:#fff}
.meta{color:#9aa6b2;font-size:.86rem}
.badge-soft{display:inline-flex;align-items:center;gap:.35rem;border-radius:999px;padding:.35rem .7rem;font-size:.72rem;font-weight:700;margin:.2rem .35rem .2rem 0}
.badge-stock{background:rgba(56,189,248,.14);color:#9adfff}
.badge-price{background:rgba(34,197,94,.16);color:#b5f5cd}
.mode-switch{display:flex;gap:.5rem;flex-wrap:wrap;margin:.85rem 0}
.mode-option{display:flex;align-items:center;gap:.45rem;padding:.45rem .7rem;border:1px solid rgba(255,255,255,.08);border-radius:999px;color:#d9e2ec}
.mode-grid{display:grid;gap:.8rem}
.sales-btn{border:0;border-radius:14px;min-height:44px;font-weight:800}
.sales-btn-primary{background:linear-gradient(135deg,#22c55e,#86efac);color:#07130b}
.sales-btn-secondary{background:transparent;border:1px solid rgba(255,255,255,.12);color:#d0d8e2}
.sales-btn-danger{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2);color:#ffb4b4}
.cart-summary{display:flex;justify-content:space-between;align-items:center;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,.08)}
.summary-total{font-size:1.5rem;font-weight:800;color:#9ff0bc}
.empty-state{padding:2rem 1rem;text-align:center;color:#98a2b3}
.muted-help{color:#8d99a6;font-size:.75rem}
.hidden{display:none!important}
.form-grid{display:grid;gap:.75rem}
.cart-config{display:grid;gap:.75rem;margin-top:.9rem}
.cart-total-line{display:flex;justify-content:space-between;align-items:center;margin-top:.85rem;padding-top:.75rem;border-top:1px solid rgba(255,255,255,.08);color:#d9e2ec}
@media (min-width:900px){.sales-grid{grid-template-columns:minmax(0,1.15fr) minmax(360px,420px)}.form-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
</style>

<div class="sales-shell">
    <h1 class="sales-title">Nouvelle vente</h1>
    <p class="sales-subtitle">Vente au poids ou par sachet/unite, avec stock et benefice calcules sur la quantite reelle en grammes/ml.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger border-0 rounded-4 mb-3"><?php echo session()->getFlashdata('error'); ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 rounded-4 mb-3"><?php echo session()->getFlashdata('success'); ?></div>
    <?php endif; ?>

    <div class="sales-grid">
        <div>
            <section class="sales-panel mb-3">
                <div class="sales-panel-body">
                    <input type="text" id="productSearch" class="form-control search-input" placeholder="Rechercher un produit...">
                </div>
            </section>

            <section class="sales-panel">
                <div class="sales-panel-body">
                    <div id="productsList" class="products-list">
                        <?php foreach (($allProducts ?? $products ?? []) as $product): ?>
                            <?php $stockDisplay = $formatQty($product['quantite_display'] ?? 0); ?>
                            <article class="product-card product-card-item"
                                data-product-id="<?php echo $product['id']; ?>"
                                data-product-name="<?php echo esc($product['nom']); ?>"
                                data-product-price="<?php echo esc($product['prix_vente_display']); ?>"
                                data-product-stock="<?php echo esc($product['quantite_display']); ?>"
                                data-product-unit="<?php echo esc($product['display_unit_label']); ?>"
                                data-product-step="<?php echo esc($product['quantity_step']); ?>">
                                <div class="product-head">
                                    <div>
                                        <div class="product-name"><?php echo esc($product['nom']); ?></div>
                                        <div class="meta">
                                            <span class="badge-soft badge-stock">Stock: <?php echo esc($stockDisplay); ?> <?php echo esc($product['display_unit_label']); ?></span>
                                            <span class="badge-soft badge-price"><?php echo number_format((float) $product['prix_vente_display'], 0, '.', ' '); ?> FCFA / <?php echo esc($product['display_unit_label']); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mode-switch">
                                    <label class="mode-option">
                                        <input type="radio" name="mode-<?php echo $product['id']; ?>" value="poids" checked>
                                        <span>Vente au poids</span>
                                    </label>
                                    <label class="mode-option">
                                        <input type="radio" name="mode-<?php echo $product['id']; ?>" value="sachet">
                                        <span>Vente par sachet</span>
                                    </label>
                                </div>

                                <div class="mode-grid weight-fields">
                                    <div class="form-grid">
                                        <div>
                                            <label class="muted-help">Quantite (<?php echo esc($product['display_unit_label']); ?>)</label>
                                            <input type="number" class="form-control sales-number weight-quantity" min="<?php echo esc($product['quantity_step']); ?>" step="<?php echo esc($product['quantity_step']); ?>" value="1">
                                        </div>
                                        <div>
                                            <label class="muted-help">Prix commercial</label>
                                            <input type="number" class="form-control sales-number weight-price" min="0.001" step="0.001" value="<?php echo esc($product['prix_vente_display']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mode-grid package-fields hidden">
                                    <div class="form-grid">
                                        <div>
                                            <label class="muted-help">Type</label>
                                            <select class="form-select sales-select package-type">
                                                <option value="sachet">Sachet</option>
                                                <option value="unite">Unite</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="muted-help">Nombre</label>
                                            <input type="number" class="form-control sales-number package-count" min="1" step="1" value="1">
                                        </div>
                                        <div>
                                            <label class="muted-help">Poids d'un sachet (<?php echo esc($product['display_unit_label']); ?>)</label>
                                            <input type="number" class="form-control sales-number package-weight" min="<?php echo esc($product['quantity_step']); ?>" step="<?php echo esc($product['quantity_step']); ?>" value="<?php echo esc($product['quantity_step'] === '1' ? '1' : '0.3'); ?>">
                                        </div>
                                        <div>
                                            <label class="muted-help">Prix d'un sachet</label>
                                            <input type="number" class="form-control sales-number package-price" min="1" step="1" value="180">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="sales-btn sales-btn-primary w-100 addToCartBtn">Ajouter au panier</button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>

        <div>
            <section class="sales-panel mb-3">
                <div class="sales-panel-body">
                    <div id="emptyCart" class="empty-state">Le panier est vide.</div>
                    <div id="cartItems" class="cart-list"></div>
                    <div class="cart-summary">
                        <div>
                            <div class="muted-help">Total</div>
                            <div id="cartTotal" class="summary-total">0 FCFA</div>
                        </div>
                        <div id="cartCount" class="meta">0 article</div>
                    </div>
                </div>
            </section>

            <form id="saleForm" method="POST" action="<?php echo base_url('sales/store'); ?>" class="sales-panel">
                <?php echo csrf_field(); ?>
                <div class="sales-panel-body">
                    <div class="form-grid">
                        <div>
                            <label class="muted-help">Nom du client</label>
                            <input type="text" id="client" name="client" class="form-control sales-input" placeholder="Ex: Junior">
                        </div>
                        <div>
                            <label class="muted-help">Mode de paiement</label>
                            <select id="paymentMethod" name="payment_method" class="form-select sales-select">
                                <option value="cash">Especes</option>
                                <option value="dette">Credit / Dette</option>
                                <option value="mobile">Mobile Money</option>
                                <option value="virement">Virement</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" id="submitSaleBtn" class="sales-btn sales-btn-primary" disabled>Valider la vente</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>

<?php echo $this->section('scripts'); ?>
<script>
const initialCart = <?php echo json_encode($cart ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

const cartData = {
    items: Array.isArray(initialCart) ? initialCart : [],

    init() {
        this.bindCatalogEvents();
        this.bindSearch();
        this.renderCart();
    },

    bindCatalogEvents() {
        document.querySelectorAll('.product-card-item').forEach((card) => {
            const radios = card.querySelectorAll('input[type="radio"]');
            radios.forEach((radio) => {
                radio.addEventListener('change', () => this.toggleModeFields(card));
            });

            card.querySelector('.addToCartBtn')?.addEventListener('click', () => this.addProductCard(card));
            this.toggleModeFields(card);
        });
    },

    bindSearch() {
        const input = document.getElementById('productSearch');
        if (!input) return;

        input.addEventListener('input', () => {
            const search = input.value.trim().toLowerCase();
            document.querySelectorAll('.product-card-item').forEach((card) => {
                const name = (card.dataset.productName || '').toLowerCase();
                card.classList.toggle('hidden', search !== '' && !name.includes(search));
            });
        });
    },

    toggleModeFields(card) {
        const mode = card.querySelector('input[type="radio"]:checked')?.value || 'poids';
        card.querySelector('.weight-fields')?.classList.toggle('hidden', mode !== 'poids');
        card.querySelector('.package-fields')?.classList.toggle('hidden', mode !== 'sachet');
    },

    getCardPayload(card) {
        const mode = card.querySelector('input[type="radio"]:checked')?.value || 'poids';
        const payload = {
            product_id: card.dataset.productId,
            mode_vente: mode,
            '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
        };

        if (mode === 'sachet') {
            payload.type_emballage = card.querySelector('.package-type')?.value || 'sachet';
            payload.nombre_sachets = card.querySelector('.package-count')?.value || '0';
            payload.poids_sachet = card.querySelector('.package-weight')?.value || '0';
            payload.prix_sachet = card.querySelector('.package-price')?.value || '0';
        } else {
            payload.quantite = card.querySelector('.weight-quantity')?.value || '0';
            payload.prix_unitaire = card.querySelector('.weight-price')?.value || '0';
        }

        return payload;
    },

    addProductCard(card) {
        this.sendCartRequest('<?php echo base_url('sales/addToCart'); ?>', this.getCardPayload(card), 'Produit ajoute au panier');
    },

    sendCartRequest(url, payload, successMessage = '') {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new URLSearchParams(payload)
        })
        .then(async (res) => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Erreur');
            return data;
        })
        .then((data) => {
            this.items = data.cart || [];
            this.renderCart();
            if (successMessage) this.showToast(successMessage);
        })
        .catch((err) => alert(err.message));
    },

    renderCart() {
        const container = document.getElementById('cartItems');
        const emptyCart = document.getElementById('emptyCart');
        const total = document.getElementById('cartTotal');
        const count = document.getElementById('cartCount');
        const submitBtn = document.getElementById('submitSaleBtn');

        const itemCount = this.items.length;
        const grandTotal = this.items.reduce((sum, item) => sum + Number(item.subtotal || 0), 0);

        count.textContent = `${itemCount} article${itemCount > 1 ? 's' : ''}`;
        total.textContent = this.formatCurrency(grandTotal);
        submitBtn.disabled = itemCount === 0;

        if (itemCount === 0) {
            emptyCart.classList.remove('hidden');
            container.innerHTML = '';
            return;
        }

        emptyCart.classList.add('hidden');
        container.innerHTML = this.items.map((item) => this.renderCartItem(item)).join('');
        this.bindCartEvents();
    },

    renderCartItem(item) {
        const isPackage = item.mode_vente === 'sachet';
        const salesLabel = item.sales_label || 'Quantite';
        const unit = item.display_unit_label || '';

        return `
            <article class="cart-card" data-product-id="${item.product_id}">
                <div class="cart-head">
                    <div>
                        <div class="cart-name">${item.product_name}</div>
                        <div class="meta">${item.mode_label || ''}</div>
                        <div class="meta">${item.detail_label || ''}</div>
                        <div class="meta">Poids total: ${this.formatQuantity(item.quantite_display)} ${unit}</div>
                    </div>
                    <button type="button" class="sales-btn sales-btn-danger removeBtn" data-product-id="${item.product_id}">Supprimer</button>
                </div>

                <div class="cart-config">
                    <div class="form-grid">
                        <div>
                            <label class="muted-help">Mode</label>
                            <select class="form-select sales-select cartMode" data-product-id="${item.product_id}">
                                <option value="poids" ${!isPackage ? 'selected' : ''}>Vente au poids</option>
                                <option value="sachet" ${isPackage ? 'selected' : ''}>Vente par sachet</option>
                            </select>
                        </div>
                        <div class="${isPackage ? '' : 'hidden'} package-type-wrap">
                            <label class="muted-help">Type</label>
                            <select class="form-select sales-select cartPackageType" data-product-id="${item.product_id}">
                                <option value="sachet" ${(item.type_emballage || 'sachet') === 'sachet' ? 'selected' : ''}>Sachet</option>
                                <option value="unite" ${(item.type_emballage || 'sachet') === 'unite' ? 'selected' : ''}>Unite</option>
                            </select>
                        </div>
                    </div>

                    <div class="weight-config ${isPackage ? 'hidden' : ''}">
                        <div class="form-grid">
                            <div>
                                <label class="muted-help">${salesLabel} (${unit})</label>
                                <input type="number" class="form-control sales-number cartWeightQty" data-product-id="${item.product_id}" value="${this.formatInput(item.quantite_display)}" min="0.001" step="${item.quantity_step || '0.001'}">
                            </div>
                            <div>
                                <label class="muted-help">Prix commercial</label>
                                <input type="number" class="form-control sales-number cartWeightPrice" data-product-id="${item.product_id}" value="${this.formatInput(item.prix_display)}" min="0.001" step="0.001">
                            </div>
                        </div>
                    </div>

                    <div class="package-config ${isPackage ? '' : 'hidden'}">
                        <div class="form-grid">
                            <div>
                                <label class="muted-help">Nombre</label>
                                <input type="number" class="form-control sales-number cartPackageCount" data-product-id="${item.product_id}" value="${this.formatInput(item.package_count_display)}" min="1" step="1">
                            </div>
                            <div>
                                <label class="muted-help">Poids d'un sachet (${unit})</label>
                                <input type="number" class="form-control sales-number cartPackageWeight" data-product-id="${item.product_id}" value="${this.formatInput(item.package_weight_display)}" min="0.001" step="${item.quantity_step || '0.001'}">
                            </div>
                            <div>
                                <label class="muted-help">Prix d'un sachet</label>
                                <input type="number" class="form-control sales-number cartPackagePrice" data-product-id="${item.product_id}" value="${this.formatInput(item.package_price_display)}" min="1" step="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cart-total-line">
                    <span>${item.commercial_price_label || ''}</span>
                    <strong>${this.formatCurrency(item.subtotal)}</strong>
                </div>
            </article>
        `;
    },

    bindCartEvents() {
        document.querySelectorAll('.removeBtn').forEach((btn) => {
            btn.onclick = () => this.sendCartRequest(
                '<?php echo base_url('sales/removeFromCart'); ?>',
                { product_id: btn.dataset.productId, '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>' },
                'Produit supprime du panier'
            );
        });

        document.querySelectorAll('.cartMode, .cartPackageType, .cartWeightQty, .cartWeightPrice, .cartPackageCount, .cartPackageWeight, .cartPackagePrice').forEach((field) => {
            field.addEventListener('change', () => this.updateCartFromDom(field.dataset.productId));
            field.addEventListener('blur', () => this.updateCartFromDom(field.dataset.productId));
        });
    },

    updateCartFromDom(productId) {
        const card = document.querySelector(`.cart-card[data-product-id="${productId}"]`);
        if (!card) return;

        const mode = card.querySelector('.cartMode')?.value || 'poids';
        const payload = {
            product_id: productId,
            mode_vente: mode,
            '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
        };

        if (mode === 'sachet') {
            payload.type_emballage = card.querySelector('.cartPackageType')?.value || 'sachet';
            payload.nombre_sachets = card.querySelector('.cartPackageCount')?.value || '0';
            payload.poids_sachet = card.querySelector('.cartPackageWeight')?.value || '0';
            payload.prix_sachet = card.querySelector('.cartPackagePrice')?.value || '0';
        } else {
            payload.quantite = card.querySelector('.cartWeightQty')?.value || '0';
            payload.prix_unitaire = card.querySelector('.cartWeightPrice')?.value || '0';
        }

        this.sendCartRequest('<?php echo base_url('sales/updateCartItem'); ?>', payload);
    },

    formatCurrency(value) {
        return `${Number(value || 0).toLocaleString('fr-FR')} FCFA`;
    },

    formatQuantity(value) {
        return Number(value || 0).toLocaleString('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 3,
        });
    },

    formatInput(value) {
        const number = Number(value || 0);
        if (!Number.isFinite(number)) return '0';
        return number.toFixed(3).replace(/\.?0+$/, '');
    },

    showToast(message) {
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.left = '50%';
        toast.style.bottom = '90px';
        toast.style.transform = 'translateX(-50%)';
        toast.style.zIndex = '9999';
        toast.innerHTML = `<div style="background:#1c8f47;color:#fff;padding:.8rem 1rem;border-radius:14px;box-shadow:0 16px 30px rgba(0,0,0,.22)">${message}</div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2200);
    }
};

document.addEventListener('DOMContentLoaded', () => cartData.init());
</script>
<?php echo $this->endSection(); ?>
