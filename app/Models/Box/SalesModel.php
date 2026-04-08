<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesModel extends Model
{
  protected $table = 'sales';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'type',
    'date',
    'time',
    'number',
    'sucursal',
    'user',
    'total_price',
    'total_cost',
    'total_margin',
    'status',
    'session',
    'customer',
    'expedition_point',
    'invoice_type',
    'cash_received',
    'cash_change'
  ];

  public function add_sales($data)
  {
    $this->insert($data);

    return $this->insertID();
  }

}