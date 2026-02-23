<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class CustomerModel extends Model
{
  protected $table = 'customers';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
    'ci',
    'cel',
    'correo',
    'user'
  ];

  public function search($value)
  {
    return $this->select('ci, name')
      ->groupStart()
      ->like('ci', $value)
      ->orLike('name', $value)
      ->groupEnd()
      ->limit(4)
      ->findAll();
  }
}