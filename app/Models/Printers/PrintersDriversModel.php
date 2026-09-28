<?php

namespace App\Models\Printers;

use CodeIgniter\Model;

class PrintersDriversModel extends Model
{
  protected $table = 'printers_drivers';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',


  ];

  public function get_PrintersDrivers()
  {
    return $this->findAll();
  }

}