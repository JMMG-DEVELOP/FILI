<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class ProductSales extends Model
{
  protected $table = 'products_sales';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];


}