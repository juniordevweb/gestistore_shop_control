<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'shop_id',
        'nom',
        'prix_achat',
        'prix_vente',
        'quantite',
        'unite_base',
        'unite_affichage',
    ];

    protected $useTimestamps = false;

    // =========================
    // PRODUITS STOCK FAIBLE
    // =========================
    public function getLowStockProducts()
    {
        return $this->where('quantite <', 5)
                    ->findAll();
    }

    // =========================
    // TOTAL PRODUITS
    // =========================
    public function getTotalProducts()
    {
        return $this->countAll();
    }

    // =========================
    // RECHERCHE PRODUIT
    // =========================
    public function searchProduct($keyword)
    {
        return $this->like('nom', $keyword)
                    ->findAll();
    }

    // =========================
    // PRODUITS PAR BOUTIQUE
    // =========================
    public function getProductsByShop($shop_id)
    {
        return $this->where('shop_id', $shop_id)
                    ->findAll();
    }

    // =========================
    // MISE À JOUR STOCK
    // =========================
    public function updateStock($id, $quantity)
    {
        return $this->update($id, [
            'quantite' => $quantity
        ]);
    }
}
