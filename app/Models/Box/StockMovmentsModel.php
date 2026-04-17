<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class StockMovmentsModel extends Model
{
  protected $table = 'stock_movements';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'product',
    'movement',
    'quantity',
    'user',
    'sucursal',
    'date',
    'operation',
    'time'
  ];

  public function add_stock_movement($values)
  {
    return $this->insertBatch($values);

  }


}