<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesDetailsModel extends Model
{
  protected $table = 'sales_details';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'product',
    'cant',
    'unit_price',
    'unit_cost',
    'total_price',
    'total_cost',
    'total_iva',
    'type_iva',
    'margin',
    'total_margin',
    'sales',
    'unit_iva',
    'base_imponible'
  ];

  public function add_sales_details($data)
  {
    return $this->insertBatch($data);
  }


  public function get_id($id)
  {
    return $this->select('
            sales_details.product,
            sales_details.cant,
            sales_details.unit_price AS price,
            sales_details.total_price AS total,
            products.description AS descripcion,
            sales.sucursal AS sucursal,
               sales.customer AS customer_id
        ')
      ->join(
        'products',
        'products.code = sales_details.product',
        'left'
      )
      ->join(
        'sales',
        'sales.id = sales_details.sales',
        'left'
      )
      ->where('sales_details.sales', $id)
      ->orderBy('sales_details.id', 'ASC')
      ->findAll();
  }

  public function getInvoiceDetails(int $saleId): array
  {
    return $this->db->table('sales_details sd')
      ->select([
        'sd.id',
        'sd.product',
        'sd.cant',
        'sd.unit_price',
        'sd.total_price',
        'sd.type_iva',
        'sd.unit_iva',
        'sd.base_imponible',
        'p.description AS descripcion'
      ])
      ->join(
        'products p',
        'p.code = sd.product',
        'left'
      )
      ->where('sd.sales', $saleId)
      ->orderBy('sd.id', 'ASC')
      ->get()
      ->getResultArray();
  }
}