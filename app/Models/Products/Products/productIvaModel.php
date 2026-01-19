<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class ProductIvaModel extends Model
{
  protected $table = 'products_iva';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];


}