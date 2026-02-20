<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class PaymentTypeModel extends Model
{
  protected $table = 'payment_type';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];


}