<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesItemModel extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'sale_id',
        'product_id',
        'product_name',
        'quantite',
        'prix_unitaire',
        'sous_total',
        'cout_total',
        'benefice',
        'unite_base',
        'unite_affichage',
        'mode_vente',
        'type_emballage',
        'quantite_saisie',
        'poids_emballage',
        'prix_emballage',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Récupérer tous les articles d'une vente
     */
    public function getSaleItems($saleId)
    {
        return $this->where('sale_id', $saleId)
                    ->findAll();
    }

    /**
     * Calculer le sous-total automatiquement
     */
    public function calculateSubtotal($quantite, $prixUnitaire)
    {
        return $quantite * $prixUnitaire;
    }
}
