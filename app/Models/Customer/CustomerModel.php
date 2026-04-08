<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomerModel extends Model
{
  protected $table = 'customers';
  protected $primaryKey = 'id';
  // protected $useAutoIncrement = false;
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
    return $this->select('id, ci, name')
      ->where('ci', $ci)
      ->first();
  }
  public function add(array $data): array
  {
    try {
      // Validaciones
      if (empty($data['ci'])) {
        return ['status' => false, 'message' => 'CI o RUC es obligatorio'];
      }

      // Verificar si existe
      if ($this->where('ci', $data['ci'])->first()) {
        return ['status' => false, 'message' => 'El cliente ya existe'];
      }

      // Insertar cliente
      $insert = $this->insert($data);
      if (!$insert) {
        return ['status' => false, 'message' => 'Error al guardar el cliente'];
      }

      return [
        'status' => true,
        'message' => 'Cliente creado correctamente',
        'data' => $this->where('ci', $data['ci'])->first()
      ];

    } catch (\Exception $e) {
      log_message('error', $e->getMessage());
      return ['status' => false, 'message' => 'Error interno del servidor'];
    }
  }
}