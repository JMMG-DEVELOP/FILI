<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class WaitDetailsModel extends Model
{
  protected $table = 'wait_details';

  protected $primaryKey = 'id_in';

  protected $returnType = 'array';

  protected $allowedFields = [
    'id',
    'cant',
    'wait',
    'price_one',
    'price_two',
    'cant_two',
    'cost',
    'iva',
    'code',
    'description'
  ];

  public function add_wait_details($data)
  {
    return $this->insertBatch($data);
  }
  public function list($wait)
  {
    return $this->where('wait', $wait)->findAll();
  }

  public function wait_delete($wait)
  {
    return $this->where('wait', $wait)->delete();
  }
}