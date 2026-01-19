<?php

namespace App\Models\Products;

use CodeIgniter\Model;

class PricesModel extends Model
{
    protected $table      = 'products_prices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'price_one',
        'price_two',
        'price_three',
        'cant_one',
        'cant_two',
        'cant_three',
        'product'
    ];

    
}