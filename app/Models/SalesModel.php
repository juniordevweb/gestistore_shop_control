<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'shop_id',
        'client',
        'total',
        'payment_method',
        'created_at'
    ];

    protected $useTimestamps = false;

    // =========================
    // CALCUL CHIFFRE D'AFFAIRES TOTAL
    // =========================
    public function getTotalRevenue($shopId = null)
    {
        $builder = $this->builder();

        if ($shopId) {
            $builder->where('shop_id', $shopId);
        }

        $row = $builder
            ->selectSum('total', 'total_amount')
            ->get()
            ->getRowArray();

        return (float) ($row['total_amount'] ?? 0);
    }

    // =========================
    // NOMBRE TOTAL DE VENTES
    // =========================
    public function getTotalSales($shopId = null)
    {
        $builder = $this->builder();

        if ($shopId) {
            $builder->where('shop_id', $shopId);
        }

        return (int) $builder->countAllResults();
    }

    // =========================
    // RÉCUPÉRER VENTES AVEC ITEMS
    // =========================
    public function getSalesWithItems($shopId = null, $limit = 50)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sales');

        if ($shopId) {
            $builder->where('shop_id', $shopId);
        }

        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->getResultArray();
    }

    // =========================
    // RÉCUPÉRER UNE VENTE COMPLÈTE
    // =========================
    public function getSaleWithItems($saleId)
    {
        $db = \Config\Database::connect();

        $sale = $this->find($saleId);
        if (!$sale) {
            return null;
        }

        $itemsModel = new SalesItemModel();
        $sale['items'] = $itemsModel->getSaleItems($saleId);

        return $sale;
    }
}
