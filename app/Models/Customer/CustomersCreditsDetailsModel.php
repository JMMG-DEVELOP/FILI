<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomersCreditsDetailsModel extends Model
{
  protected $table = 'customer_credits_details';
  protected $primaryKey = 'id';
  // protected $useAutoIncrement = false;
  protected $returnType = 'array';

  protected $allowedFields = [
    'credit',
    'credit_type',
    'mount',
    'date',
    'time'
  ];

  public function add_CustomersCreditsDetailsModel($values)
  {
    $this->insert($values);
    return $this->getInsertID();
  }
}