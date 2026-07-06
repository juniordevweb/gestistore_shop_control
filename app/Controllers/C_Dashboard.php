<?php

namespace App\Controllers;

use App\Libraries\InventoryUnitService;
use App\Models\M_Product;
use App\Models\UserModel;
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
        if ((int) session()->get('is_admin') === 1) {
            return view('V_admin_dashboard', $this->buildAdminDashboardData());
        }

        return view('V_dashboard', $this->buildShopDashboardData());
    }

    private function buildShopDashboardData(): array
    {
        $shopId = session()->get('shop_id');
        $productModel = new M_Product();
        $salesModel = new SalesModel();
        $db = \Config\Database::connect();

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

        $products = array_map(fn(array $product) => $this->unitService->enrichProductForDisplay($product), $products);
        $lowStock = count(array_filter($products, fn(array $product) => $this->unitService->isLowStock($product)));
        $dailySales = $dailySalesRow['total'] ?? 0;
        $dailyProfit = $dailyProfitRow['profit'] ?? 0;

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
                'icon'    => 'fa-user',
            ];
        }

        return [
            'products'       => $products,
            'total_products' => $totalProducts,
            'low_stock'      => $lowStock,
            'total_sales'    => $totalSales,
            'daily_sales'    => $dailySales,
            'daily_profit'   => $dailyProfit,
            'recent_sales'   => $recent_sales,
            'active_debts'   => $active_debts,
        ];
    }

    private function buildAdminDashboardData(): array
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();
        $productModel = new M_Product();
        $salesModel = new SalesModel();
        $shopProfileRows = $db->table('shop_profiles')
            ->select('shop_id, shop_name, address, phone, website')
            ->get()
            ->getResultArray();
        $shopProfiles = [];

        foreach ($shopProfileRows as $profile) {
            $shopProfiles[(string) ($profile['shop_id'] ?? '')] = $profile;
        }

        $shops = $userModel
            ->select('id, shop_id, email, account_status, created_at')
            ->where('users.is_admin', 0)
            ->orderBy('users.created_at', 'DESC')
            ->findAll();

        $totalShops = (new UserModel())->where('is_admin', 0)->countAllResults();
        $approvedShops = (new UserModel())->where('is_admin', 0)->where('account_status', 'approved')->countAllResults();
        $pendingShops = (new UserModel())->where('is_admin', 0)->where('account_status', 'pending')->countAllResults();
        $rejectedShops = (new UserModel())->where('is_admin', 0)->where('account_status', 'rejected')->countAllResults();

        $totalProducts = $productModel->getTotalProducts();
        $lowStockProducts = (new M_Product())->where('quantite <', 5)->countAllResults();
        $totalSales = $salesModel->getTotalSales();
        $totalRevenue = $salesModel->getTotalRevenue();
        $totalDebtRow = $db->table('sales')
            ->selectSum('total', 'debt_total')
            ->where('payment_method', 'dette')
            ->get()
            ->getRowArray();
        $totalDebt = (float) ($totalDebtRow['debt_total'] ?? 0);

        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $dailySalesRow = $db->table('sales')
            ->selectSum('total', 'daily_revenue')
            ->where('created_at >=', $todayStart)
            ->where('created_at <=', $todayEnd)
            ->get()
            ->getRowArray();
        $dailyProfitRow = $db->table('sale_items si')
            ->select('SUM(si.benefice) AS profit', false)
            ->join('sales s', 's.id = si.sale_id')
            ->where('s.created_at >=', $todayStart)
            ->where('s.created_at <=', $todayEnd)
            ->get()
            ->getRowArray();

        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $monthKey = date('Y-m');
        $monthSalesRows = $db->table('sales')
            ->select('DATE(created_at) AS sale_date, SUM(total) AS revenue', false)
            ->where('created_at >=', $monthStart . ' 00:00:00')
            ->where('created_at <=', $monthEnd . ' 23:59:59')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get()
            ->getResultArray();
        $monthProfitRows = $db->table('sale_items si')
            ->select('DATE(s.created_at) AS sale_date, SUM(si.benefice) AS profit', false)
            ->join('sales s', 's.id = si.sale_id')
            ->where('s.created_at >=', $monthStart . ' 00:00:00')
            ->where('s.created_at <=', $monthEnd . ' 23:59:59')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get()
            ->getResultArray();

        $salesByShopRows = $db->table('sales')
            ->select('shop_id, COUNT(*) AS sales_count, SUM(total) AS revenue', false)
            ->groupBy('shop_id')
            ->get()
            ->getResultArray();
        $profitByShopRows = $db->table('sale_items si')
            ->select('s.shop_id, SUM(si.benefice) AS profit', false)
            ->join('sales s', 's.id = si.sale_id')
            ->groupBy('s.shop_id')
            ->get()
            ->getResultArray();
        $debtByShopRows = $db->table('sales')
            ->select('shop_id, SUM(total) AS debt_total', false)
            ->where('payment_method', 'dette')
            ->groupBy('shop_id')
            ->get()
            ->getResultArray();
        $productByShopRows = $db->table('products')
            ->select('shop_id, COUNT(*) AS product_count, SUM(CASE WHEN quantite < 5 THEN 1 ELSE 0 END) AS low_stock_count, SUM(quantite) AS stock_total', false)
            ->groupBy('shop_id')
            ->get()
            ->getResultArray();

        $shopIndex = [];
        foreach ($shops as $shop) {
            $shopId = (string) ($shop['shop_id'] ?? '');
            $profile = $shopProfiles[$shopId] ?? [];
            $shopName = trim((string) ($profile['shop_name'] ?? ''));
            if ($shopName === '') {
                $shopName = 'Boutique ' . strtoupper(substr($shopId, 0, 6));
            }

            $shopIndex[$shopId] = [
                'id' => (int) ($shop['id'] ?? 0),
                'shop_id' => $shopId,
                'shop_name' => $shopName,
                'email' => $shop['email'] ?? '',
                'account_status' => $shop['account_status'] ?? 'approved',
                'created_at' => $shop['created_at'] ?? null,
                'address' => $profile['address'] ?? '',
                'phone' => $profile['phone'] ?? '',
                'website' => $profile['website'] ?? '',
                'sales_count' => 0,
                'revenue' => 0.0,
                'profit' => 0.0,
                'debt' => 0.0,
                'product_count' => 0,
                'low_stock_count' => 0,
                'stock_total' => 0,
            ];
        }

        foreach ($salesByShopRows as $row) {
            $shopId = (string) ($row['shop_id'] ?? '');
            if (! isset($shopIndex[$shopId])) {
                continue;
            }
            $shopIndex[$shopId]['sales_count'] = (int) ($row['sales_count'] ?? 0);
            $shopIndex[$shopId]['revenue'] = (float) ($row['revenue'] ?? 0);
        }

        foreach ($profitByShopRows as $row) {
            $shopId = (string) ($row['shop_id'] ?? '');
            if (! isset($shopIndex[$shopId])) {
                continue;
            }
            $shopIndex[$shopId]['profit'] = (float) ($row['profit'] ?? 0);
        }

        foreach ($debtByShopRows as $row) {
            $shopId = (string) ($row['shop_id'] ?? '');
            if (! isset($shopIndex[$shopId])) {
                continue;
            }
            $shopIndex[$shopId]['debt'] = (float) ($row['debt_total'] ?? 0);
        }

        foreach ($productByShopRows as $row) {
            $shopId = (string) ($row['shop_id'] ?? '');
            if (! isset($shopIndex[$shopId])) {
                continue;
            }
            $shopIndex[$shopId]['product_count'] = (int) ($row['product_count'] ?? 0);
            $shopIndex[$shopId]['low_stock_count'] = (int) ($row['low_stock_count'] ?? 0);
            $shopIndex[$shopId]['stock_total'] = (float) ($row['stock_total'] ?? 0);
        }

        $shopsSummary = array_values($shopIndex);
        usort($shopsSummary, fn(array $a, array $b) => $b['revenue'] <=> $a['revenue']);

        $recentShops = array_slice($shops, 0, 6);

        $recentSalesRows = $db->table('sales s')
            ->select('s.id, s.client, s.total, s.payment_method, s.created_at, s.shop_id, sp.shop_name', false)
            ->join('shop_profiles sp', 'sp.shop_id = s.shop_id', 'left')
            ->orderBy('s.created_at', 'DESC')
            ->limit(8)
            ->get()
            ->getResultArray();

        $monthTrend = [];
        $daysInMonth = (int) date('t', strtotime($monthStart));
        $salesByDate = array_column($monthSalesRows, 'revenue', 'sale_date');
        $profitByDate = array_column($monthProfitRows, 'profit', 'sale_date');
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%s-%02d', $monthKey, $day);
            $monthTrend[] = [
                'date' => $date,
                'label' => date('d M', strtotime($date)),
                'revenue' => (float) ($salesByDate[$date] ?? 0),
                'profit' => (float) ($profitByDate[$date] ?? 0),
            ];
        }

        $recent_sales = [];
        foreach ($recentSalesRows as $sale) {
            $recent_sales[] = [
                'shop_name' => trim((string) ($sale['shop_name'] ?? '')) ?: 'Boutique',
                'client' => $sale['client'] ?? 'Client',
                'montant' => (float) ($sale['total'] ?? 0),
                'payment_method' => $sale['payment_method'] ?? 'cash',
                'heure' => isset($sale['created_at']) ? date('H:i', strtotime($sale['created_at'])) : '--:--',
            ];
        }

        $pending_users = array_values(array_filter($shops, fn(array $shop) => ($shop['account_status'] ?? '') === 'pending'));

        return [
            'summary' => [
                'total_shops' => $totalShops,
                'approved_shops' => $approvedShops,
                'pending_shops' => $pendingShops,
                'rejected_shops' => $rejectedShops,
                'total_products' => $totalProducts,
                'low_stock_products' => $lowStockProducts,
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'total_debt' => $totalDebt,
                'daily_revenue' => (float) ($dailySalesRow['daily_revenue'] ?? 0),
                'daily_profit' => (float) ($dailyProfitRow['profit'] ?? 0),
            ],
            'shops_summary' => $shopsSummary,
            'recent_shops' => $recentShops,
            'recent_sales' => $recent_sales,
            'monthly_trend' => $monthTrend,
            'pending_users' => $pending_users,
            'month_label' => date('F Y'),
            'admin_email' => session()->get('email'),
        ];
    }
}
