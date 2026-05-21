<?php

namespace App\Controllers;

use App\Libraries\InventoryUnitService;
use App\Models\M_Product;
use App\Models\SalesItemModel;
use App\Models\SalesModel;

class Sales extends BaseController
{
    private InventoryUnitService $unitService;

    public function __construct()
    {
        $this->unitService = new InventoryUnitService();
    }

    private function findAccessibleProduct($productId)
    {
        $productModel = new M_Product();

        if (session()->get('is_admin')) {
            return $productModel->find($productId);
        }

        return $productModel
            ->where('shop_id', session()->get('shop_id'))
            ->find($productId);
    }

    private function cartTotal(array $cart): string
    {
        return array_reduce(
            $cart,
            fn($sum, $item) => $this->unitService->add($sum, (string) ($item['subtotal'] ?? '0')),
            '0'
        );
    }

    private function normalizeSaleMode(?string $mode): string
    {
        return ($mode === 'sachet') ? 'sachet' : 'poids';
    }

    private function normalizePackageType(?string $type): string
    {
        return ($type === 'unite') ? 'unite' : 'sachet';
    }

    private function buildWeightSaleData(array $product, string $quantityDisplay, string $priceDisplay): array
    {
        if ($this->unitService->compare($quantityDisplay, '0') <= 0) {
            throw new \InvalidArgumentException('Quantite invalide.');
        }

        if ($this->unitService->compare($priceDisplay, '0') <= 0) {
            throw new \InvalidArgumentException('Prix invalide.');
        }

        $quantityBase = $this->unitService->toBaseQuantity($quantityDisplay, $product['unite_affichage']);
        $priceBase = $this->unitService->toBaseUnitPrice($priceDisplay, $product['unite_affichage']);
        $subtotal = $this->unitService->multiply($quantityBase, $priceBase);

        return [
            'mode_vente' => 'poids',
            'type_emballage' => 'sachet',
            'quantite' => $quantityBase,
            'quantite_saisie' => $quantityDisplay,
            'poids_emballage' => '0',
            'prix_emballage' => '0',
            'prix' => $priceBase,
            'prix_display' => $priceDisplay,
            'subtotal' => $subtotal,
        ];
    }

    private function buildPackageSaleData(array $product, string $packageCount, string $packageWeightDisplay, string $packagePrice): array
    {
        if ($this->unitService->compare($packageCount, '0') <= 0) {
            throw new \InvalidArgumentException('Nombre de sachets invalide.');
        }

        if ($this->unitService->compare($packageWeightDisplay, '0') <= 0) {
            throw new \InvalidArgumentException('Poids du sachet invalide.');
        }

        if ($this->unitService->compare($packagePrice, '0') <= 0) {
            throw new \InvalidArgumentException('Prix du sachet invalide.');
        }

        $packageWeightBase = $this->unitService->toBaseQuantity($packageWeightDisplay, $product['unite_affichage']);
        $totalWeightBase = $this->unitService->multiply($packageCount, $packageWeightBase);
        $subtotal = $this->unitService->multiply($packageCount, $packagePrice);
        $effectiveBasePrice = $this->unitService->divide($subtotal, $totalWeightBase);

        return [
            'mode_vente' => 'sachet',
            'type_emballage' => 'sachet',
            'quantite' => $totalWeightBase,
            'quantite_saisie' => $packageCount,
            'poids_emballage' => $packageWeightBase,
            'prix_emballage' => $packagePrice,
            'prix' => $effectiveBasePrice,
            'prix_display' => $this->unitService->fromBaseUnitPrice($effectiveBasePrice, $product['unite_affichage']),
            'subtotal' => $subtotal,
        ];
    }

    private function buildSaleDataFromRequest(array $product): array
    {
        $mode = $this->normalizeSaleMode($this->request->getPost('mode_vente'));

        if ($mode === 'sachet') {
            return $this->buildPackageSaleData(
                $product,
                (string) ($this->request->getPost('nombre_sachets') ?: '0'),
                (string) ($this->request->getPost('poids_sachet') ?: '0'),
                (string) ($this->request->getPost('prix_sachet') ?: '0')
            );
        }

        return $this->buildWeightSaleData(
            $product,
            (string) ($this->request->getPost('quantite') ?: '0'),
            (string) ($this->request->getPost('prix_unitaire') ?: '0')
        );
    }

    private function rebuildCartItemFromMode(array $item): array
    {
        $product = $this->findAccessibleProduct($item['product_id'] ?? null);
        $product = $product ? $this->unitService->enrichProductForDisplay($product) : [
            'nom' => $item['product_name'] ?? 'Produit',
            'unite_base' => $item['unite_base'] ?? 'g',
            'unite_affichage' => $item['unite_affichage'] ?? 'kg',
            'display_unit_label' => $this->unitService->displayLabel($item['unite_affichage'] ?? 'kg'),
            'quantity_step' => $this->unitService->getQuantityStep($item['unite_affichage'] ?? 'kg'),
        ];

        $item['product_name'] = $item['product_name'] ?? $product['nom'];
        $item['unite_base'] = $item['unite_base'] ?? ($product['unite_base'] ?? 'g');
        $item['unite_affichage'] = $item['unite_affichage'] ?? ($product['unite_affichage'] ?? 'kg');
        $item['display_unit_label'] = $product['display_unit_label'] ?? $this->unitService->displayLabel($item['unite_affichage']);
        $item['quantity_step'] = $product['quantity_step'] ?? $this->unitService->getQuantityStep($item['unite_affichage']);
        $item['mode_vente'] = $this->normalizeSaleMode($item['mode_vente'] ?? 'poids');
        $item['type_emballage'] = $this->normalizePackageType($item['type_emballage'] ?? 'sachet');
        $item['quantite'] = (string) ($item['quantite'] ?? '0');
        $item['subtotal'] = (string) ($item['subtotal'] ?? '0');
        $item['quantite_saisie'] = (string) ($item['quantite_saisie'] ?? '0');
        $item['poids_emballage'] = (string) ($item['poids_emballage'] ?? '0');
        $item['prix_emballage'] = (string) ($item['prix_emballage'] ?? '0');
        $item['quantite_display'] = $this->unitService->fromBaseQuantity($item['quantite'], $item['unite_affichage']);

        if ($item['mode_vente'] === 'sachet') {
            $item['package_count_display'] = $item['quantite_saisie'];
            $item['package_weight_display'] = $this->unitService->fromBaseQuantity($item['poids_emballage'], $item['unite_affichage']);
            $item['package_price_display'] = $item['prix_emballage'];
            $item['sales_label'] = $item['type_emballage'] === 'unite' ? 'Unites' : 'Sachets';
            $item['mode_label'] = $item['type_emballage'] === 'unite' ? 'Vente par unite' : 'Vente par sachet';
            $item['commercial_quantity_label'] = $this->unitService->trimDecimal($item['quantite_saisie']) . ' ' . ($item['type_emballage'] === 'unite' ? 'unite(s)' : 'sachet(s)');
            $item['commercial_price_label'] = $this->unitService->trimDecimal($item['prix_emballage']) . ' FCFA / ' . ($item['type_emballage'] === 'unite' ? 'unite' : 'sachet');
            $item['detail_label'] = $item['commercial_quantity_label'] . ' x ' . $this->unitService->trimDecimal($item['package_weight_display']) . ' ' . $item['display_unit_label'];
        } else {
            $item['prix_display'] = $this->unitService->fromBaseUnitPrice((string) ($item['prix'] ?? '0'), $item['unite_affichage']);
            $item['package_count_display'] = '0';
            $item['package_weight_display'] = '0';
            $item['package_price_display'] = '0';
            $item['sales_label'] = 'Quantite';
            $item['mode_label'] = 'Vente au poids';
            $item['commercial_quantity_label'] = $this->unitService->trimDecimal($item['quantite_display']) . ' ' . $item['display_unit_label'];
            $item['commercial_price_label'] = $this->unitService->trimDecimal($item['prix_display']) . ' FCFA / ' . $item['display_unit_label'];
            $item['detail_label'] = $item['commercial_quantity_label'];
        }

        return $item;
    }

    private function normalizeCart(array $cart): array
    {
        return array_values(array_map(fn(array $item) => $this->rebuildCartItemFromMode($item), $cart));
    }

    public function create()
    {
        $isAdmin = session()->get('is_admin');
        $productModel = new M_Product();
        $allProductsModel = new M_Product();

        if ($isAdmin) {
            $data['products'] = $productModel->orderBy('id', 'ASC')->limit(3)->findAll();
            $data['allProducts'] = $allProductsModel->orderBy('nom', 'ASC')->findAll();
        } else {
            $shopId = session()->get('shop_id');
            $data['products'] = $productModel->where('shop_id', $shopId)->orderBy('id', 'ASC')->limit(3)->findAll();
            $data['allProducts'] = $allProductsModel->where('shop_id', $shopId)->orderBy('nom', 'ASC')->findAll();
        }

        $data['products'] = array_map(fn(array $product) => $this->unitService->enrichProductForDisplay($product), $data['products']);
        $data['allProducts'] = array_map(fn(array $product) => $this->unitService->enrichProductForDisplay($product), $data['allProducts']);
        $data['cart'] = $this->normalizeCart(session()->get('cart') ?? []);
        session()->set('cart', $data['cart']);

        return view('V_sales', $data);
    }

    public function searchProducts()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Requete non valide']);
        }

        $keyword = trim((string) $this->request->getGet('q'));
        if ($keyword === '') {
            return $this->response->setJSON(['results' => []]);
        }

        $productModel = new M_Product();
        if (! session()->get('is_admin')) {
            $productModel->where('shop_id', session()->get('shop_id'));
        }

        $products = $productModel
            ->like('nom', $keyword)
            ->orderBy('nom', 'ASC')
            ->findAll(10);

        return $this->response->setJSON([
            'results' => array_map(function ($product) {
                $product = $this->unitService->enrichProductForDisplay($product);

                return [
                    'id' => $product['id'],
                    'nom' => $product['nom'],
                    'prix_vente' => (float) $product['prix_vente_display'],
                    'quantite' => (float) $product['quantite_display'],
                    'unite_affichage' => $product['unite_affichage'],
                    'display_unit_label' => $product['display_unit_label'],
                    'quantity_step' => $product['quantity_step'],
                ];
            }, $products),
        ]);
    }

    public function addToCart()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Request non valide']);
        }

        $productId = $this->request->getPost('product_id');
        $product = $this->findAccessibleProduct($productId);

        if (! $product) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Produit introuvable']);
        }

        $product = $this->unitService->enrichProductForDisplay($product);

        try {
            $saleData = $this->buildSaleDataFromRequest($product);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON(['error' => $e->getMessage()]);
        }

        if ($this->unitService->compare($saleData['quantite'], $product['quantite']) > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => "Stock insuffisant. Stock actuel : {$product['quantite_display']} {$product['display_unit_label']}",
            ]);
        }

        $cart = session()->get('cart') ?? [];
        $cart = array_values(array_filter($cart, fn($item) => (string) $item['product_id'] !== (string) $productId));

        $cart[] = [
            'product_id' => $productId,
            'product_name' => $product['nom'],
            'quantite' => $saleData['quantite'],
            'prix' => $saleData['prix'],
            'subtotal' => $saleData['subtotal'],
            'unite_base' => $product['unite_base'],
            'unite_affichage' => $product['unite_affichage'],
            'mode_vente' => $saleData['mode_vente'],
            'type_emballage' => $saleData['type_emballage'],
            'quantite_saisie' => $saleData['quantite_saisie'],
            'poids_emballage' => $saleData['poids_emballage'],
            'prix_emballage' => $saleData['prix_emballage'],
        ];

        $cart = $this->normalizeCart($cart);
        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Produit ajoute au panier',
            'cart' => $cart,
            'total' => $this->cartTotal($cart),
            'count' => count($cart),
        ]);
    }

    public function updateCartItem()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Request non valide']);
        }

        $productId = $this->request->getPost('product_id');
        $mode = $this->normalizeSaleMode($this->request->getPost('mode_vente'));
        $cart = session()->get('cart') ?? [];
        $product = $this->findAccessibleProduct($productId);

        if (! $product) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Produit introuvable']);
        }

        $product = $this->unitService->enrichProductForDisplay($product);

        try {
            if ($mode === 'sachet') {
                $saleData = $this->buildPackageSaleData(
                    $product,
                    (string) ($this->request->getPost('nombre_sachets') ?: '0'),
                    (string) ($this->request->getPost('poids_sachet') ?: '0'),
                    (string) ($this->request->getPost('prix_sachet') ?: '0')
                );
                $saleData['type_emballage'] = $this->normalizePackageType($this->request->getPost('type_emballage'));
            } else {
                $saleData = $this->buildWeightSaleData(
                    $product,
                    (string) ($this->request->getPost('quantite') ?: '0'),
                    (string) ($this->request->getPost('prix_unitaire') ?: '0')
                );
            }
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(400)->setJSON(['error' => $e->getMessage()]);
        }

        if ($this->unitService->compare($saleData['quantite'], $product['quantite']) > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => "Stock insuffisant. Stock actuel : {$product['quantite_display']} {$product['display_unit_label']}",
            ]);
        }

        foreach ($cart as $index => $item) {
            if ((string) $item['product_id'] !== (string) $productId) {
                continue;
            }

            $cart[$index] = array_merge($item, [
                'quantite' => $saleData['quantite'],
                'prix' => $saleData['prix'],
                'subtotal' => $saleData['subtotal'],
                'mode_vente' => $saleData['mode_vente'],
                'type_emballage' => $saleData['type_emballage'],
                'quantite_saisie' => $saleData['quantite_saisie'],
                'poids_emballage' => $saleData['poids_emballage'],
                'prix_emballage' => $saleData['prix_emballage'],
            ]);
            break;
        }

        $cart = $this->normalizeCart($cart);
        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'cart' => $cart,
            'total' => $this->cartTotal($cart),
            'count' => count($cart),
        ]);
    }

    public function updateCartPrice()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Request non valide']);
        }

        return $this->updateCartItem();
    }

    public function removeFromCart()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Request non valide']);
        }

        $productId = $this->request->getPost('product_id');
        $cart = session()->get('cart') ?? [];
        $cart = array_values(array_filter($cart, fn($item) => (string) $item['product_id'] !== (string) $productId));
        $cart = $this->normalizeCart($cart);
        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'cart' => $cart,
            'total' => $this->cartTotal($cart),
            'count' => count($cart),
        ]);
    }

    public function store()
    {
        $isAdmin = session()->get('is_admin');
        $shopId = $isAdmin ? ($this->request->getPost('shop_id') ?: 'admin-shop') : session()->get('shop_id');
        $client = trim((string) $this->request->getPost('client'));
        $payment = $this->request->getPost('payment_method') ?: 'cash';
        $cart = $this->normalizeCart(session()->get('cart') ?? []);

        if ($cart === []) {
            return redirect()->back()->with('error', 'Le panier est vide');
        }

        if ($payment === 'dette' && $client === '') {
            return redirect()->back()->with('error', 'Le nom du client est obligatoire pour une dette');
        }

        if ($client === '') {
            $client = 'Acheteur';
        }

        foreach ($cart as $item) {
            $product = $this->findAccessibleProduct($item['product_id']);

            if (! $product || $this->unitService->compare((string) $product['quantite'], (string) $item['quantite']) < 0) {
                return redirect()->back()->with('error', "Stock insuffisant pour {$item['product_name']}");
            }
        }

        $grandTotal = $this->cartTotal($cart);
        $salesModel = new SalesModel();
        $salesItemModel = new SalesItemModel();
        $productModel = new M_Product();

        $salesModel->insert([
            'shop_id' => $shopId,
            'client' => $client,
            'total' => $grandTotal,
            'payment_method' => $payment,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $saleId = $salesModel->getInsertID();

        foreach ($cart as $item) {
            $product = $productModel->find($item['product_id']);
            $purchasePriceBase = (string) ($product['prix_achat'] ?? '0');
            $costTotal = $this->unitService->multiply($item['quantite'], $purchasePriceBase);
            $profit = $this->unitService->subtract($item['subtotal'], $costTotal);

            $salesItemModel->insert([
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantite' => $item['quantite'],
                'prix_unitaire' => $item['prix'],
                'sous_total' => $item['subtotal'],
                'cout_total' => $costTotal,
                'benefice' => $profit,
                'unite_base' => $item['unite_base'],
                'unite_affichage' => $item['unite_affichage'],
                'mode_vente' => $item['mode_vente'],
                'type_emballage' => $item['type_emballage'],
                'quantite_saisie' => $item['quantite_saisie'],
                'poids_emballage' => $item['poids_emballage'],
                'prix_emballage' => $item['prix_emballage'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($product) {
                $newStock = $this->unitService->subtract((string) $product['quantite'], (string) $item['quantite']);

                if ($this->unitService->compare($newStock, '0') < 0) {
                    $newStock = '0';
                }

                $productModel->update($item['product_id'], ['quantite' => $newStock]);
            }
        }

        session()->remove('cart');

        return redirect()->to('/sales/list')
            ->with('success', "Vente #$saleId enregistree avec succes");
    }

    public function list()
    {
        $isAdmin = session()->get('is_admin');
        $salesModel = new SalesModel();

        if ($isAdmin) {
            $data['sales'] = $salesModel->orderBy('created_at', 'DESC')->findAll();
        } else {
            $shopId = session()->get('shop_id');
            $data['sales'] = $salesModel
                ->where('shop_id', $shopId)
                ->orderBy('created_at', 'DESC')
                ->findAll();
        }

        return view('V_sales_list', $data);
    }

    public function detail($saleId)
    {
        $isAdmin = session()->get('is_admin');
        $salesModel = new SalesModel();
        $salesItemModel = new SalesItemModel();
        $sale = $salesModel->find($saleId);

        if (! $sale) {
            return redirect()->to('/sales/list')
                ->with('error', 'Vente introuvable');
        }

        if (! $isAdmin && $sale['shop_id'] != session()->get('shop_id')) {
            return redirect()->to('/sales/list')
                ->with('error', 'Acces non autorise');
        }

        $data['sale'] = $sale;
        $data['items'] = array_map(function (array $item) {
            $item = $this->rebuildCartItemFromMode($item);
            $item['prix_unitaire_display'] = $item['mode_vente'] === 'sachet'
                ? $item['prix_emballage']
                : $this->unitService->fromBaseUnitPrice($item['prix_unitaire'], $item['unite_affichage']);

            return $item;
        }, $salesItemModel->getSaleItems($saleId));

        return view('V_sales_detail', $data);
    }
}
