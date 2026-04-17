<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class BoxMovementModel extends Model
{
  protected $table = 'box_movement';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'type',
    'payment',
    'mount',
    'box',
    'sales'
  ];

  public function add_box_movement($values)
  {
    return $this->insert($values);
  }


}