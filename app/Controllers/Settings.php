<?php

namespace App\Controllers;

class Settings extends BaseController
{
    private function getShopProfile(string $shopId): array
    {
        $shopProfileModel = new \App\Models\ShopProfileModel();
        $profile = $shopProfileModel->where('shop_id', $shopId)->first();

        if ($profile) {
            return $profile;
        }

        $defaultProfile = [
            'shop_id' => $shopId,
            'shop_name' => 'Ma boutique',
            'address' => '',
            'phone' => '',
            'website' => '',
        ];

        $shopProfileModel->insert($defaultProfile);

        return $shopProfileModel->where('shop_id', $shopId)->first() ?? $defaultProfile;
    }

    private function buildSalesPeriodReport(?string $shopId = null): array
    {
        $salesModel = new \App\Models\SalesModel();
        $db = \Config\Database::connect();

        $startInput = trim((string) $this->request->getGet('from'));
        $endInput = trim((string) $this->request->getGet('to'));

        $query = $db->table('sales');
        if ($shopId) {
            $query->where('shop_id', $shopId);
        }

        $firstSaleRow = $query->selectMin('created_at', 'first_date')->get()->getRowArray();
        $firstSaleDate = $firstSaleRow['first_date'] ?? null;

        $defaultStart = $firstSaleDate ? date('Y-m-d', strtotime($firstSaleDate)) : date('Y-m-d');
        $defaultEnd = date('Y-m-d');

        $startDate = $startInput !== '' ? $startInput : $defaultStart;
        $endDate = $endInput !== '' ? $endInput : $defaultEnd;

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $periodQuery = $db->table('sales');
        if ($shopId) {
            $periodQuery->where('shop_id', $shopId);
        }

        $periodRow = $periodQuery
            ->selectSum('total', 'total_amount')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        $recetteQuery = $db->table('sales');
        if ($shopId) {
            $recetteQuery->where('shop_id', $shopId);
        }

        $recetteRow = $recetteQuery
            ->selectSum('total', 'recette_amount')
            ->where('payment_method !=', 'dette')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        // Somme des ventes à crédit (dettes) pour cette période
        $detteQuery = $db->table('sales');
        if ($shopId) {
            $detteQuery->where('shop_id', $shopId);
        }

        $detteRow = $detteQuery
            ->selectSum('total', 'dette_amount')
            ->where('payment_method', 'dette')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        $countQuery = $db->table('sales');
        if ($shopId) {
            $countQuery->where('shop_id', $shopId);
        }

        $salesCount = (int) $countQuery
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->countAllResults();

        return [
            'from' => $startDate,
            'to' => $endDate,
            'total' => (float) ($periodRow['total_amount'] ?? 0),
            'recette' => (float) max(0, ((float) ($periodRow['total_amount'] ?? 0) - (float) ($detteRow['dette_amount'] ?? 0))),
            'dette' => (float) ($detteRow['dette_amount'] ?? 0),
            'count' => $salesCount,
            'first_sale' => $defaultStart,
        ];
    }

    public function index()
    {
        $isAdmin = session()->get('is_admin');

        if ($isAdmin) {
            return $this->adminSettings();
        }

        return $this->shopSettings();
    }

    private function adminSettings()
    {
        $userModel = new \App\Models\UserModel();
        $productModel = new \App\Models\M_Product();
        $salesModel = new \App\Models\SalesModel();

        $users = $userModel->findAll();
        $pendingUsers = $userModel
            ->where('is_admin', 0)
            ->where('account_status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $totalShops = $userModel->where('is_admin', 0)->countAllResults();
        $totalProducts = $productModel->getTotalProducts();
        $totalRevenue = $salesModel->getTotalRevenue();
        $totalSales = $salesModel->getTotalSales();
        $salesPeriod = $this->buildSalesPeriodReport(null);

        $data = [
            'title' => 'Parametres Admin',
            'users' => $users,
            'pending_users' => $pendingUsers,
            'stats' => [
                'total_shops' => $totalShops,
                'total_products' => $totalProducts,
                'total_revenue' => $totalRevenue,
                'total_sales' => $totalSales,
            ],
            'sales_period' => $salesPeriod,
        ];

        return view('V_settings_admin', $data);
    }

    private function shopSettings()
    {
        $shopId = session()->get('shop_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('shop_id', $shopId)->first();
        $shopProfile = $this->getShopProfile($shopId);
        $salesPeriod = $this->buildSalesPeriodReport($shopId);

        $data = [
            'title' => 'Parametres Boutique',
            'user' => $user,
            'shop_profile' => $shopProfile,
            'sales_period' => $salesPeriod,
        ];

        return view('V_settings_shop', $data);
    }

    public function revenueReport()
    {
        $isAdmin = session()->get('is_admin');
        $shopId = session()->get('shop_id');
        $db = \Config\Database::connect();

        $year = (int) ($this->request->getGet('year') ?: date('Y'));
        $month = (int) ($this->request->getGet('month') ?: date('m'));
        if ($month < 1 || $month > 12) {
            $month = (int) date('m');
        }
        if ($year < 2000) {
            $year = (int) date('Y');
        }

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $salesQuery = $db->table('sales');
        if (! $isAdmin) {
            $salesQuery->where('shop_id', $shopId);
        }

        $salesRows = $salesQuery
            ->select('DATE(created_at) AS sale_date, SUM(total) AS revenue', false)
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get()
            ->getResultArray();

        $profitQuery = $db->table('sale_items si')
            ->select('DATE(s.created_at) AS sale_date, SUM(si.benefice) AS profit', false)
            ->join('sales s', 's.id = si.sale_id')
            ->where('s.created_at >=', $startDate . ' 00:00:00')
            ->where('s.created_at <=', $endDate . ' 23:59:59');

        if (! $isAdmin) {
            $profitQuery->where('s.shop_id', $shopId);
        }

        $profitRows = $profitQuery
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get()
            ->getResultArray();

        $salesByDate = array_column($salesRows, 'revenue', 'sale_date');
        $profitByDate = array_column($profitRows, 'profit', 'sale_date');

        $daysInMonth = (int) date('t', strtotime($startDate));
        $dailyData = [];
        $totalRevenue = 0;
        $totalProfit = 0;

        $monthNames = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $revenue = (float) ($salesByDate[$date] ?? 0);
            $profit = (float) ($profitByDate[$date] ?? 0);
            $dailyData[] = [
                'date' => $date,
                'label' => sprintf('%02d %s', $day, $monthNames[sprintf('%02d', $month)]),
                'revenue' => $revenue,
                'profit' => $profit,
            ];
            $totalRevenue += $revenue;
            $totalProfit += $profit;
        }

        $data = [
            'daily_data' => $dailyData,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'title' => 'Chiffre d\'affaires du mois',
            'month_label' => sprintf('%s %04d', $monthNames[sprintf('%02d', $month)], $year),
        ];

        return view('V_settings_revenue_month', $data);
    }

    public function updateProfile()
    {
        $userId = session()->get('user_id');

        $rules = [
            'email' => 'required|valid_email',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $userModel = new \App\Models\UserModel();
        $userModel->update($userId, [
            'email' => $this->request->getPost('email'),
        ]);

        session()->set('email', $this->request->getPost('email'));

        return redirect()->back()->with('success', 'Profil mis a jour.');
    }

    public function updateShopInfo()
    {
        $shopId = session()->get('shop_id');
        $shopProfileModel = new \App\Models\ShopProfileModel();

        $rules = [
            'shop_name' => 'required|min_length[2]|max_length[255]',
            'address' => 'permit_empty|max_length[255]',
            'phone' => 'permit_empty|max_length[50]',
            'website' => 'permit_empty|valid_url_strict|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $payload = [
            'shop_name' => trim((string) $this->request->getPost('shop_name')),
            'address' => trim((string) $this->request->getPost('address')),
            'phone' => trim((string) $this->request->getPost('phone')),
            'website' => trim((string) $this->request->getPost('website')),
        ];

        $existing = $shopProfileModel->where('shop_id', $shopId)->first();
        if ($existing) {
            $shopProfileModel->update($existing['id'], $payload);
        } else {
            $payload['shop_id'] = $shopId;
            $shopProfileModel->insert($payload);
        }

        return redirect()->back()->with('success', 'Informations de la boutique mises a jour.');
    }

    public function changePassword()
    {
        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (! password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('error', 'Mot de passe actuel incorrect.');
        }

        $userModel->update($userId, [
            'password_hash' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->back()->with('success', 'Mot de passe change.');
    }

    public function toggleUserStatus($userId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Acces refuse.');
        }

        return redirect()->back()->with('success', 'Statut utilisateur mis a jour.');
    }

    public function approveUser($userId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Acces refuse.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (! $user || (int) ($user['is_admin'] ?? 0) === 1) {
            return redirect()->back()->with('error', 'Utilisateur introuvable.');
        }

        $userModel->update($userId, ['account_status' => 'approved']);

        return redirect()->back()->with('success', 'Compte utilisateur confirme.');
    }

    public function rejectUser($userId)
    {
        if (! session()->get('is_admin')) {
            return redirect()->to('/')->with('error', 'Acces refuse.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (! $user || (int) ($user['is_admin'] ?? 0) === 1) {
            return redirect()->back()->with('error', 'Utilisateur introuvable.');
        }

        $userModel->update($userId, ['account_status' => 'rejected']);

        return redirect()->back()->with('success', 'Demande utilisateur annulee.');
    }
}
