<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class CardPercentModel extends Model
{
  protected $table = 'card_percent';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'cant',
    'name',
  ];


}