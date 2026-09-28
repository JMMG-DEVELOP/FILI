<?php

namespace App\Models\Printers;

use CodeIgniter\Model;

class PrintersCharsetsModel extends Model
{
  protected $table = 'printers_charsets';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
    'code',
    'description'

  ];

  public function get_all()
  {
    return $this->findAll();
  }

}