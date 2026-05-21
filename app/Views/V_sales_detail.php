<?php echo $this->extend('V_layout'); ?>

<?php echo $this->section('content'); ?>

<?php
$formatQty = static function ($value): string {
    return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
};
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">Detail de la vente #<?php echo $sale['id']; ?></h1>
            <p class="text-muted small mb-0"><?php echo date('d/m/Y H:i', strtotime($sale['created_at'])); ?></p>
        </div>
        <a href="<?php echo base_url('sales/list'); ?>" class="btn btn-outline-secondary">Retour</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Client</div>
                            <div class="fw-bold"><?php echo esc($sale['client']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Paiement</div>
                            <div class="fw-bold"><?php echo esc($sale['payment_method']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-dark text-white">Articles</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Mode</th>
                                    <th>Vente commerciale</th>
                                    <th class="text-end">Poids total</th>
                                    <th class="text-end">Cout reel</th>
                                    <th class="text-end">Benefice</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo esc($item['product_name']); ?></td>
                                        <td><?php echo esc($item['mode_label'] ?? ''); ?></td>
                                        <td>
                                            <div><?php echo esc($item['detail_label'] ?? ''); ?></div>
                                            <small class="text-muted"><?php echo esc($item['commercial_price_label'] ?? ''); ?></small>
                                        </td>
                                        <td class="text-end"><?php echo esc($formatQty($item['quantite_display'] ?? 0)); ?> <?php echo esc($item['display_unit_label'] ?? ''); ?></td>
                                        <td class="text-end"><?php echo number_format((float) ($item['cout_total'] ?? 0), 0, '.', ' '); ?> FCFA</td>
                                        <td class="text-end"><?php echo number_format((float) ($item['benefice'] ?? 0), 0, '.', ' '); ?> FCFA</td>
                                        <td class="text-end fw-bold text-success"><?php echo number_format((float) ($item['sous_total'] ?? 0), 0, '.', ' '); ?> FCFA</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">Resume</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Articles</span>
                        <strong><?php echo count($items); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Poids total</span>
                        <strong><?php echo esc($formatQty(array_sum(array_map(fn($item) => (float) ($item['quantite_display'] ?? 0), $items)))); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Benefice total</span>
                        <strong><?php echo number_format(array_sum(array_map(fn($item) => (float) ($item['benefice'] ?? 0), $items)), 0, '.', ' '); ?> FCFA</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="fw-bold">TOTAL</span>
                        <span class="h4 mb-0 text-success"><?php echo number_format((float) $sale['total'], 0, '.', ' '); ?> FCFA</span>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-3">
                <a href="<?php echo base_url('sales/create'); ?>" class="btn btn-success">Nouvelle vente</a>
                <button class="btn btn-outline-primary" onclick="window.print()">Imprimer</button>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>
