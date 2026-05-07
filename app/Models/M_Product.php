<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'shop_id',
        'nom',
        'prix_achat',
        'prix_vente',
        'quantite'
    ];
}