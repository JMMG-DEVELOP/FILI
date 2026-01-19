<?php

namespace App\Models\Products;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table      = 'products_stock';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'stock',
        'sucursal',
        'product'
    ];

    
}