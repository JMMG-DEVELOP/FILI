<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class ExpeditionPointModel extends Model
{
  protected $table = 'expedition_point';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'code',
    'sucursal'
  ];

  public function actualizate_document_secquence($expedition_point)
  {

  }
  public function actualizate_invoice_secquence($expedition_point)
  {

  }
}