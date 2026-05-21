<?php

namespace App\Controllers;

use App\Models\SalesModel;

class Debts extends BaseController
{
    public function index()
    {
        $isAdmin = session()->get('is_admin');
        $salesModel = new SalesModel();

        if ($isAdmin) {
            $rawDebts = $salesModel
                ->where('payment_method', 'dette')
                ->orderBy('id', 'DESC')
                ->findAll();
        } else {
            $shopId = session()->get('shop_id');
            $rawDebts = $salesModel
                ->where('shop_id', $shopId)
                ->where('payment_method', 'dette')
                ->orderBy('id', 'DESC')
                ->findAll();
        }

        $debts = [];

        foreach ($rawDebts as $debt) {
            $debts[] = [
                'id'       => $debt['id'],
                'client'   => $debt['client'] ?: 'Client inconnu',
                'montant'  => (float)$debt['total'],
                'type'     => 'Dette client',
                'status'   => 'En attente',
                'class'    => 'danger',
                'icon'     => 'fa-user',
            ];
        }

        $data = [
            'active_debts' => $debts
        ];

        return view('V_debts', $data);
    }

    public function delete($id)
    {
        $isAdmin = session()->get('is_admin');
        $salesModel = new SalesModel();

        if ($isAdmin) {
            $salesModel->delete($id);
        } else {
            $shopId = session()->get('shop_id');
            $salesModel->where('shop_id', $shopId)->delete($id);
        }

        return redirect()->to('/debts')
                         ->with('success', 'Dette supprimée');
    }
}