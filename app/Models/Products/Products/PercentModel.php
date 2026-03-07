<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class PercentModel extends Model
{
  protected $table = 'percent';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'cant',
  ];


}