<?php

namespace App\Models\Customer;

use CodeIgniter\Model;
use PhpParser\Builder\Function_;

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

  public function verify_customer_credit_status($customer)
  {
    $result = $this->db->table('customers')
      ->select('credit_status')
      ->where('id', $customer)
      ->get()
      ->getRowArray();

    return $result && (int) $result['credit_status'] === 1;
  }
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
      return [
        'status' => true,
        'id' => $credit['id']
      ];
    }

    // SI NO EXISTE -> INSERTAR
    $insert = $this->insert([
      'customer' => $data['customer'],
      'amount' => $data['amount'],
    ]);

    if (!$insert) {

      return [
        'status' => false,
        'errors' => $this->errors(),
        'db' => $this->db->error(),
        'data' => $data
      ];
    }

    // retornar nuevo ID
    return [
      'status' => true,
      'id' => $this->insertID()
    ];
  }
  public function customer_credit_null($customer, $amount)
  {
    $credit = $this
      ->where('customer', $customer)
      ->first();

    if (!$credit) {
      return false;
    }

    $currentAmount = (float) $credit['amount'];
    $amount = (float) $amount;

    $newAmount = $currentAmount - $amount;

    // Evitar saldo negativo
    if ($newAmount < 0) {
      $newAmount = 0;
    }

    $updated = $this->update(
      $credit['id'],
      [
        'amount' => $newAmount
      ]
    );

    if (!$updated) {
      return false;
    }

    return [
      'status' => true,
      'id' => $credit['id'],
      'customer' => $customer,
      'old_amount' => $currentAmount,
      'amount' => $amount,
      'new_amount' => $newAmount
    ];
  }
  // public function add_sales($data)
  // {
  //   if (!$this->insert($data)) {

  //     return [
  //       'status' => false,
  //       'errors' => $this->errors(),
  //       'db' => $this->db->error(),
  //       'data' => $data
  //     ];
  //   }

  //   return [
  //     'status' => true,
  //     'id' => $this->insertID()
  //   ];
  // }
}