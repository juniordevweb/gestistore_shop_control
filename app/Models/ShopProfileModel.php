<?php

namespace App\Models;

use CodeIgniter\Model;

class ShopProfileModel extends Model
{
    protected $table = 'shop_profiles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'shop_id',
        'shop_name',
        'address',
        'phone',
        'website',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
