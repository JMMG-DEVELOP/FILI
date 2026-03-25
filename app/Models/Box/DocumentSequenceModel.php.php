<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class ExpeditionPointModel extends Model
{
  protected $table = 'document_sequence';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'last_number',
    'expedition_point'
  ];


}