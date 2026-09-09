<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomersCreditsDetailsModel extends Model
{
  protected $table = 'customer_credits_details';
  protected $primaryKey = 'id';
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
    if (!$this->insert($values)) {
      return false;
    }

    return $this->getInsertID();
  }
}