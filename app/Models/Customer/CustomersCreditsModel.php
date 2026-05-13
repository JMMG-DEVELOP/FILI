<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomersCreditsModel extends Model
{
  protected $table = 'customers_credits';
  protected $primaryKey = 'id';
  // protected $useAutoIncrement = false;
  protected $returnType = 'array';

  protected $allowedFields = [
    'amount',
    'customer',
  ];

  public function customer_credit($data)
  {
    // Buscar crédito existente del cliente
    $credit = $this->where('customer', $data['customer'])->first();

    // SI EXISTE -> SUMAR MONTO
    if ($credit) {

      $newAmount = $credit['amount'] + $data['amount'];

      $updated = $this->update($credit['id'], [
        'amount' => $newAmount
      ]);

      if (!$updated) {
        return false;
      }

      // retornar ID existente
      return $credit['id'];
    }

    // SI NO EXISTE -> INSERTAR
    $insert = $this->insert([
      'customer' => $data['customer'],
      'amount' => $data['amount'],
    ]);

    if (!$insert) {
      return false;
    }

    // retornar nuevo ID
    return $this->getInsertID();
  }
}