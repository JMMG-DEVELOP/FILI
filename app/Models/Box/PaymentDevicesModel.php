<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class PaymentDevicesModel extends Model
{
  protected $table = 'payment_devices';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];


}