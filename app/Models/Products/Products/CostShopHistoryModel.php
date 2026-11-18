<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class CostShopHistoryModel extends Model
{
    protected $table = 'cost_shop_history';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'price',
        'date',
        'user',
        'provider',
        'cost',
        'quantity'
    ];




}