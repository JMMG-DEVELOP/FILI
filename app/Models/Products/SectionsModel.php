<?php

namespace App\Models\Products;

use CodeIgniter\Model;

class SectionsModel extends Model
{
    protected $table      = 'products_section';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name'
    ];

    
}