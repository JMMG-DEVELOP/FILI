<?php

namespace App\Models\Printers;

use CodeIgniter\Model;

class PrintersPaperModel extends Model
{
  protected $table = 'printers_paper';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',


  ];

  public function get_PrintersPaper()
  {
    return $this->findAll();
  }

}