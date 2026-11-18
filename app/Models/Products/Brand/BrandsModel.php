<?php

namespace App\Models\Products\Brand;

use CodeIgniter\Model;

class BrandsModel extends Model
{
    protected $table = 'products_brands';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name'
    ];


}