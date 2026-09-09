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
    return $this->select(' sales_details.product, sales_details.cant, sales_details.unit_price AS price, sales_details.total_price AS total, products.description AS descripcion ')
      ->join('products', 'products.code = sales_details.product', 'left')
      ->where('sales_details.sales', $id)->orderBy('sales_details.id', 'ASC')->findAll();
  }

}