<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class DevicesModel extends Model
{
  protected $table = 'devices';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'uuid',
    'name',
    'type',
    'enabled',
  ];


}