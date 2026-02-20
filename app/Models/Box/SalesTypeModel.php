<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesTypeModel extends Model
{
  protected $table = 'sales_type';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];


}