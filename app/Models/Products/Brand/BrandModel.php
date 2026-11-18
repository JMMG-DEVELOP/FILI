<?php

namespace App\Models\Products\Brand;

use CodeIgniter\Model;

class BrandModel extends Model
{
  protected $table = 'products_brands';
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