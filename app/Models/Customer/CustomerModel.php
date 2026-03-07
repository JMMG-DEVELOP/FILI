<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomerModel extends Model
{
  protected $table = 'customers';
  protected $primaryKey = 'ci';
  protected $useAutoIncrement = false;
  protected $returnType = 'array';

  protected $allowedFields = [
    'ci',
    'name',
    'cel',
    'correo',
    'user'
  ];

  public function search($value)
  {
    return $this->select('ci, name')
      ->groupStart()
      ->like('ci', $value)
      ->orLike('name', $value)
      ->groupEnd()
      ->limit(4)
      ->findAll();
  }

  public function search_ci($ci)
  {
    return $this->select('ci, name')
      ->where('ci', $ci)
      ->first();
  }
  public function add(array $data): array
  {
    try {

      if (empty($data['ci'])) {
        return [
          'status' => false,
          'message' => 'CI o RUC es obligatorio'
        ];
      }

      if ($this->find($data['ci'])) {
        return [
          'status' => false,
          'message' => 'El cliente ya existe'
        ];
      }

      if (!$this->insert($data)) {
        return [
          'status' => false,
          'message' => 'Error al guardar el cliente'
        ];
      }

      return [
        'status' => true,
        'message' => 'Cliente creado correctamente',
        'data' => $this->find($data['ci'])
      ];

    } catch (\Exception $e) {

      log_message('error', $e->getMessage());

      return [
        'status' => false,
        'message' => 'Error interno del servidor'
      ];
    }
  }
}