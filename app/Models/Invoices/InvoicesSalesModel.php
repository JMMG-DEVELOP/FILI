<?php

namespace App\Models\Invoices;

use CodeIgniter\Model;

class InvoicesSalesModel extends Model
{
  protected $table = 'invoices_sales';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'invoice',
    'sales',
  ];

  /**
   * Obtener todos los detalles de las ventas
   * asociadas a una factura.
   */
  public function get_detail(int $invoiceId): array
  {
    return $this->db->table('invoices_sales ins')
      ->select([
        'ins.sales AS sale_id',
        'sd.product',
        'sd.cant',
        'p.description AS descripcion',
        'sd.unit_price AS price',
        'sd.total_price AS total',
      ])
      ->join(
        'sales_details sd',
        'sd.sales = ins.sales',
        'inner'
      )
      ->join(
        'products p',
        'p.code = sd.product',
        'left'
      )
      ->where('ins.invoice', $invoiceId)
      ->orderBy('ins.sales', 'ASC')
      ->orderBy('sd.id', 'ASC')
      ->get()
      ->getResultArray();
  }
}
