<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class InvoicesSalesModel extends Model
{
  protected $table = 'invoices_sales';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'invoice',
    'sales'
  ];

  public function add($values)
  {
    if (
      empty($values['invoice']) ||
      empty($values['sales'])
    ) {
      return false;
    }

    try {
      return $this->insert([
        'invoice' => $values['invoice'],
        'sales' => $values['sales']
      ], true);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR AL RELACIONAR FACTURA CON VENTA: ' .
        $e->getMessage()
      );

      return false;
    }
  }

}