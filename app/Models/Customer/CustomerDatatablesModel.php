<?php

namespace App\Models\Customer;

use App\Models\BaseDatatableModel;

class CustomerDatatablesModel extends BaseDatatableModel
{
  protected $table = 'customers';

  protected $primaryKey = 'id';

  protected $returnType = 'array';

  protected array $select = [
    'customers.id',
    'customers.ci',
    'customers.name',
    'customers.cel',
    'customers.correo',
    'customers.credit_status'
  ];

  protected array $columnOrder = [
    0 => 'customers.id',
    1 => 'customers.ci',
    2 => 'customers.name',
    3 => 'customers.cel',
    4 => 'customers.correo',
    5 => 'customers.credit_status'
  ];

  protected array $columnSearch = [
    'customers.ci',
    'customers.name',
    'customers.cel',
    'customers.correo'
  ];

  protected array $columnSearchCount = [
    'customers.ci',
    'customers.name',
    'customers.cel',
    'customers.correo'
  ];

  protected array $defaultOrder = [
    'customers.id' => 'DESC'
  ];
}
