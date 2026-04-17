<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesDetailsModel extends Model
{
  protected $table = 'sales_details';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'product',
    'cant',
    'unit_price',
    'unit_cost',
    'total_price',
    'total_cost',
    'total_iva',
    'type_iva',
    'margin',
    'total_margin',
    'sales',
    'unit_iva',
    'base_imponible'
  ];

  public function add_sales_details($data)
  {
    return $this->insertBatch($data);
  }

}