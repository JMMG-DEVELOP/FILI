<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomerPaymentsModel extends Model
{
  protected $table = 'customer_payments';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'customer_credit',
    'date',
    'time',
    'amount',
    'user'
  ];

  /**
   * Registrar pago / movimiento de crédito.
   */
  public function add_payment($values)
  {
    if (!$this->insert($values)) {
      return false;
    }

    return $this->getInsertID();
  }
}