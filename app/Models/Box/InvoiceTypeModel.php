<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class InvoiceTypeModel extends Model
{
  protected $table = 'invoice_type';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'name',
  ];


}