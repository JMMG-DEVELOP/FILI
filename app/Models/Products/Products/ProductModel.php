<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'code',
        'description',
        'section',
        'brand',
        'price',
        'cost',
        'sales',
        'status',
        'iva',

    ];


}