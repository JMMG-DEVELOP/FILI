<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesPaymentsModel extends Model
{
  protected $table = 'sales_payments';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'type',
    'amount',
    'sales',
    'total',
    'diference'
  ];

  public function add_sales_payment($values)
  {
    return $this->insert($values);
  }


}