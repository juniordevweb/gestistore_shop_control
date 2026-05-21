<?php echo $this->extend('V_layout'); ?>

<?php echo $this->section('content'); ?>

<div class="container-fluid py-4">
    <!-- HEADER -->
    <div class="mb-4">
        <h1 class="h3 mb-2 fw-bold">
            <i class="fas fa-history me-2 text-success"></i>Historique des Ventes
        </h1>
        <p class="text-muted small">Consultez et gérez toutes vos ventes</p>
    </div>

    <!-- ALERTE -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- TABLEAU VENTES -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-receipt me-2"></i>Liste des Ventes
            </h5>
        </div>

        <div class="card-body p-0">
            <?php if (empty($sales)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                    <p class="mb-0">Aucune vente enregistrée</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Client</th>
                                <th>Total</th>
                                <th>Paiement</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-success">#<?php echo $sale['id']; ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($sale['client']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success"><?php echo number_format($sale['total'], 0, '.', ' '); ?> FCFA</span>
                                    </td>
                                    <td>
                                        <?php
                                            $paymentBadge = match($sale['payment_method']) {
                                                'cash' => '<span class="badge bg-success">💰 Espèces</span>',
                                                'dette' => '<span class="badge bg-warning">📝 Crédit</span>',
                                                'mobile' => '<span class="badge bg-info">📱 Mobile</span>',
                                                'virement' => '<span class="badge bg-primary">🏦 Virement</span>',
                                                default => '<span class="badge bg-secondary">' . htmlspecialchars($sale['payment_method']) . '</span>'
                                            };
                                            echo $paymentBadge;
                                        ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($sale['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url('sales/detail/' . $sale['id']); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- BOUTON NOUVELLE VENTE -->
    <div class="mt-4">
        <a href="<?php echo base_url('sales/create'); ?>" class="btn btn-success btn-lg w-100">
            <i class="fas fa-plus-circle me-2"></i>Nouvelle Vente
        </a>
    </div>
</div>

<?php echo $this->endSection(); ?>
