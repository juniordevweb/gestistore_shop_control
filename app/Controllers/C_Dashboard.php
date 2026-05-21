<?php

namespace App\Controllers;

use App\Libraries\InventoryUnitService;
use App\Models\M_Product;
use App\Models\SalesModel;

class C_Dashboard extends BaseController
{
    private InventoryUnitService $unitService;

    public function __construct()
    {
        $this->unitService = new InventoryUnitService();
    }

    public function index()
    {
        $isAdmin = session()->get('is_admin');
        $productModel = new M_Product();
        $salesModel   = new SalesModel();
        $db           = \Config\Database::connect();

        if ($isAdmin) {
            $products = $productModel->findAll();
            $totalProducts = $productModel->countAll();
            $totalSales = $salesModel->getTotalSales();
            $dailySalesRow = $salesModel
                ->select('SUM(total) as total')
                ->where('created_at >=', date('Y-m-d 00:00:00'))
                ->where('created_at <=', date('Y-m-d 23:59:59'))
                ->first();
            $dailyProfitRow = $db->table('sale_items si')
                ->select('SUM(si.benefice) AS profit', false)
                ->join('sales s', 's.id = si.sale_id')
                ->where('s.created_at >=', date('Y-m-d 00:00:00'))
                ->where('s.created_at <=', date('Y-m-d 23:59:59'))
                ->get()
                ->getRowArray();
            $recentSales = $salesModel
                ->orderBy('id', 'DESC')
                ->findAll(5);
            $debtsRaw = $salesModel
                ->select('client, SUM(total) as montant')
                ->where('payment_method', 'dette')
                ->groupBy('client')
                ->findAll();
        } else {
            $shopId = session()->get('shop_id');
            $products = $productModel->where('shop_id', $shopId)->findAll();
            $totalProducts = $productModel
                ->where('shop_id', $shopId)
                ->countAllResults();
            $totalSales = $salesModel->getTotalSales($shopId);
            $dailySalesRow = $salesModel
                ->select('SUM(total) as total')
                ->where('shop_id', $shopId)
                ->where('created_at >=', date('Y-m-d 00:00:00'))
                ->where('created_at <=', date('Y-m-d 23:59:59'))
                ->first();
            $dailyProfitRow = $db->table('sale_items si')
                ->select('SUM(si.benefice) AS profit', false)
                ->join('sales s', 's.id = si.sale_id')
                ->where('s.shop_id', $shopId)
                ->where('s.created_at >=', date('Y-m-d 00:00:00'))
                ->where('s.created_at <=', date('Y-m-d 23:59:59'))
                ->get()
                ->getRowArray();
            $recentSales = $salesModel
                ->where('shop_id', $shopId)
                ->orderBy('id', 'DESC')
                ->findAll(5);
            $debtsRaw = $salesModel
                ->select('client, SUM(total) as montant')
                ->where('shop_id', $shopId)
                ->where('payment_method', 'dette')
                ->groupBy('client')
                ->findAll();
        }

        $products = array_map(fn(array $product) => $this->unitService->enrichProductForDisplay($product), $products);
        $lowStock = count(array_filter($products, fn(array $product) => $this->unitService->isLowStock($product)));
        $dailySales = $dailySalesRow['total'] ?? 0;
        $dailyProfit = $dailyProfitRow['profit'] ?? 0;
        $productNames = [];

        foreach ($products as $product) {
            $productNames[$product['id']] = $product['nom'] ?? 'Produit';
        }

        $recent_sales = [];

        foreach ($recentSales as $sale) {
            $recent_sales[] = [
                'client'  => $sale['client'] ?? 'Client',
                'produit' => 'Vente #' . $sale['id'],
                'montant' => (float) $sale['total'],
                'heure'   => isset($sale['created_at'])
                    ? date('H:i', strtotime($sale['created_at']))
                    : '--:--',
            ];
        }

        $active_debts = [];

        foreach ($debtsRaw as $d) {
            $active_debts[] = [
                'client'  => $d['client'],
                'montant' => $d['montant'],
                'type'    => 'Dette client',
                'status'  => 'En attente',
                'class'   => 'warning',
                'icon'    => 'fa-user'
            ];
        }

        $data = [
            'products'       => $products,
            'total_products' => $totalProducts,
            'low_stock'      => $lowStock,

            'total_sales'    => $totalSales,
            'daily_sales'    => $dailySales,
            'daily_profit'   => $dailyProfit,

            'recent_sales'   => $recent_sales,
            'active_debts'   => $active_debts,
        ];

        return view('V_dashboard', $data);
    }
}
