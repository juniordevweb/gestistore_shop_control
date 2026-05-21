<?php

namespace App\Controllers;

use App\Libraries\InventoryUnitService;
use App\Models\M_Product;

class C_Product extends BaseController
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

        if ($isAdmin) {
            $products = $productModel->findAll();
        } else {
            $shopId = session()->get('shop_id');
            $products = $productModel->where('shop_id', $shopId)->findAll();
        }

        $products = array_map(fn(array $product) => $this->unitService->enrichProductForDisplay($product), $products);
        $low_stock = count(array_filter($products, fn(array $product) => $this->unitService->isLowStock($product)));

        $data = [
            'products' => $products,
            'low_stock' => $low_stock,
        ];

        return view('V_products', $data);
    }

    public function store()
    {
        $isAdmin = session()->get('is_admin');
        $productModel = new M_Product();

        if ($isAdmin) {
            $shopId = $this->request->getPost('shop_id') ?: 'admin-shop';
        } else {
            $shopId = session()->get('shop_id');
        }

        $displayUnit = $this->request->getPost('unite_affichage') ?: 'g';
        $baseUnit = $this->unitService->getBaseUnit($displayUnit);

        $productModel->save([
            'shop_id' => $shopId,
            'nom' => $this->request->getPost('nom'),
            'prix_achat' => $this->unitService->toBaseUnitPrice((string) $this->request->getPost('prix_achat'), $displayUnit),
            'prix_vente' => $this->unitService->toBaseUnitPrice((string) $this->request->getPost('prix_vente'), $displayUnit),
            'quantite' => $this->unitService->toBaseQuantity((string) $this->request->getPost('quantite'), $displayUnit),
            'unite_base' => $baseUnit,
            'unite_affichage' => $this->unitService->normalizeUnit($displayUnit),
        ]);

        return redirect()->to('/stock');
    }

    public function edit($id)
    {
        $isAdmin = session()->get('is_admin');
        $productModel = new M_Product();

        if ($isAdmin) {
            $product = $productModel->find($id);
        } else {
            $shopId = session()->get('shop_id');
            $product = $productModel
                ->where('shop_id', $shopId)
                ->find($id);
        }

        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produit introuvable.');
        }

        return view('V_product_edit', ['product' => $this->unitService->enrichProductForDisplay($product)]);
    }

    public function update($id)
    {
        $isAdmin = session()->get('is_admin');
        $productModel = new M_Product();

        if ($isAdmin) {
            $product = $productModel->find($id);
        } else {
            $shopId = session()->get('shop_id');
            $product = $productModel
                ->where('shop_id', $shopId)
                ->find($id);
        }

        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produit introuvable.');
        }

        $displayUnit = $this->request->getPost('unite_affichage') ?: ($product['unite_affichage'] ?? $product['unite_base'] ?? 'g');
        $baseUnit = $this->unitService->getBaseUnit($displayUnit);

        $productModel->update($id, [
            'nom' => $this->request->getPost('nom'),
            'prix_achat' => $this->unitService->toBaseUnitPrice((string) $this->request->getPost('prix_achat'), $displayUnit),
            'prix_vente' => $this->unitService->toBaseUnitPrice((string) $this->request->getPost('prix_vente'), $displayUnit),
            'quantite' => $this->unitService->toBaseQuantity((string) $this->request->getPost('quantite'), $displayUnit),
            'unite_base' => $baseUnit,
            'unite_affichage' => $this->unitService->normalizeUnit($displayUnit),
        ]);

        return redirect()->to('/stock')->with('success', 'Produit modifie avec succes.');
    }

    public function delete($id)
    {
        $isAdmin = session()->get('is_admin');
        $productModel = new M_Product();

        if ($isAdmin) {
            $productModel->delete($id);
        } else {
            $shopId = session()->get('shop_id');
            $productModel->where('shop_id', $shopId)->delete($id);
        }

        return redirect()->to('/stock');
    }
}
