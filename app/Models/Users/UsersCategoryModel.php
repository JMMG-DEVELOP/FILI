<?php

namespace App\Models\Users;

use CodeIgniter\Model;

class UsersCategoryModel extends Model
{
  protected $table = 'users_category';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];
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
  public function get_All()
  {
    return $this->findAll();
  }


}