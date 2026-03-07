<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class PercentModel extends Model
{
  protected $table = 'percent';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'cant',
    'name'
  ];


}