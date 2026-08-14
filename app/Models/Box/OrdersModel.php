<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class OrdersModel extends Model
{
  protected $table = 'orders';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'product',
    'date',
    'time',
    'user',
    'status'
  ];
  public function getByCode($code)
  {
    $data = $this->where('product', $code)
      ->where('status', 1)
      ->first();

    return $data ?: false;
  }

  public function order_add($data)
  {
    // Buscar si ya existe el producto
    $exists = $this->where('product', $data['product'])
      ->first();

    // Si existe y está activo (status 1), no permitir duplicado
    if ($exists && (int) $exists['status'] === 1) {
      return false;
    }

    // Si no existe o existe con status 2, agregar
    $id = $this->insert($data);

    return $id ? true : false;
  }
}