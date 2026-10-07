<?php

namespace App\Models\Point;

use CodeIgniter\Model;

class SucursalsModel extends Model
{
  protected $table = 'sucursals';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];

  public function get_all()
  {
    return $this->findAll();
  }

}