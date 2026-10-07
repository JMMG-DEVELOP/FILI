<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class StatusModel extends Model
{
  protected $table = 'status';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name'
  ];

  public function get_All()
  {
    return $this->findAll();
  }
}