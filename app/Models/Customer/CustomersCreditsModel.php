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
  /**
   * Obtener el crédito de un cliente por su ID.
   */
  public function get_ByCustomer($customerId)
  {
    if (empty($customerId) || !is_numeric($customerId)) {
      return null;
    }

    return $this->select([
      'id',
      'amount',
      'customer',
    ])
      ->where('customer', (int) $customerId)
      ->first();
  }
  public function get_ById($customerId)
  {
    if (empty($customerId) || !is_numeric($customerId)) {
      return null;
    }

    return $this->select([
      'id',
      'amount',
      'customer',
    ])
      ->where('id', (int) $customerId)
      ->first();
  }
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

  public function getSalesCard(int $customerId): array
  {
    $builder = $this->db->table('sales s');

    $builder->select("
        COALESCE(SUM(s.total_price), 0) AS total_sales,

        COALESCE(SUM(s.total_cost), 0) AS total_cost,

        COALESCE(SUM(s.total_margin), 0) AS total_margin,

        COUNT(s.id) AS total_purchases,

        COALESCE(
            SUM(
                CASE
                    WHEN s.type = 2 THEN s.total_price
                    ELSE 0
                END
            ),
            0
        ) AS total_credit,

        COUNT(
            CASE
                WHEN s.type = 2 THEN s.id
                ELSE NULL
            END
        ) AS total_credit_purchases
    ", false);

    $builder->where('s.customer', $customerId);
    $builder->where('s.status', 1);

    return $builder->get()->getRowArray() ?? [];
  }

}