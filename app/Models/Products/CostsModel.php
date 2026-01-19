<?php

namespace App\Models\Products;

use CodeIgniter\Model;

class CostsModel extends Model
{
    protected $table      = 'products_cost';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'cost',
        'other_cost',
        'product'
    ];

    
}