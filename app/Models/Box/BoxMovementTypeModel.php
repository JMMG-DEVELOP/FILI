<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class BoxMovementTypeModel extends Model
{
  protected $table = 'box_movement_type';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];

  public function add_box_movement($values)
  {
    return $this->insert($values);
  }


}