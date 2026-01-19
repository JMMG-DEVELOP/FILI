<?php

namespace App\Models\Products;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table      = 'products_sales';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name'
    ];

    
}