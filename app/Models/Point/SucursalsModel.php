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

  public function quit($id)
  {
    if (empty($id)) {
      return false;
    }

    return $this->delete($id);
  }
  public function add($values)
  {
    return $this->insert($values);
  }
  public function edit($values, $id)
  {
    if (empty($id)) {
      return false;
    }

    return $this->update($id, $values);
  }

  public function get_ById($values)
  {
    return $this->where('id', $values)->first();
  }
}