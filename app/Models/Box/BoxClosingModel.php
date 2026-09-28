<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class BoxClosingModel extends Model
{
  protected $table = 'box_closing';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'box',
    'payment',
    'system_mount',
    'close_mount',
    'diference_mount'
  ];



}