<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CreditsSalesDetailsModel extends Model
{
  protected $table = 'credits_sales_details';
  protected $primaryKey = 'id';
  // protected $useAutoIncrement = false;
  protected $returnType = 'array';

  protected $allowedFields = [
    'credit_detail',
    'sales',
  ];

  public function add_values($values)
  {
    return $this->insert($values);
  }
}