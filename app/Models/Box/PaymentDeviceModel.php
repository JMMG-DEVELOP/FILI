<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class PaymentDeviceModel extends Model
{
  protected $table = 'payment_device';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];

  public function add_payment_device($values)
  {
    return $this->insert($values);
  }


}