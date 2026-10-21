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
    'user',
    'detail'
  ];
  public function get_ById()
  {

  }
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
  public function payment(array $values)
  {
    if (empty($values['customer_credit']) || !isset($values['amount'])) {
      return false;
    }

    if (!$this->insert($values)) {
      return false;
    }

    return $this->getInsertID();
  }
}