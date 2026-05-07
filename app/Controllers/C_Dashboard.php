<?php

namespace App\Controllers;

use App\Models\M_Product;

class C_Dashboard extends BaseController
{
   public function index()
{
    $productModel = new \App\Models\M_Product();

    $data = [
        'total_products' => $productModel->countAll(),
        'low_stock' => $productModel->where('quantite <', 5)->countAllResults(),
        'products' => $productModel->findAll()
    ];

    return view('V_dashboard', $data);
}
}