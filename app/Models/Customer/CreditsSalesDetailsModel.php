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

  /**
   * Obtener la relación entre la venta y el crédito.
   *
   * También obtiene el customer desde sales.
   */
  public function credit_sales_get($sale_id)
  {
    return $this
      ->select('credits_sales_details.*, sales.customer')
      ->join(
        'sales',
        'sales.id = credits_sales_details.sales'
      )
      ->where(
        'credits_sales_details.sales',
        $sale_id
      )
      ->findAll();
  }
}