<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomerModel extends Model
{
  protected $table = 'customers_credits';
  protected $primaryKey = 'id';
  // protected $useAutoIncrement = false;
  protected $returnType = 'array';

  protected $allowedFields = [
    'amount',
    'customer',
  ];
}