<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class ExpeditionPointModel extends Model
{
  protected $table = 'expedition_point';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'code',
    'sucursal'
  ];


}