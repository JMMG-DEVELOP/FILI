<?php

namespace App\Models\Point;

use CodeIgniter\Model;

class DocumentTypeModel extends Model
{
  protected $table = 'document_type';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];

  public function get_all()
  {
    return $this->findAll();
  }

}