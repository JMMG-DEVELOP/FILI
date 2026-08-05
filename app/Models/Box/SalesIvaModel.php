<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesIvaModel extends Model
{
  protected $table = 'sales_iva';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'type',
    'mount',
    'sales'
  ];

  public function add_sales_iva($data)
  {
    return $this->insertBatch($data);

  }



}