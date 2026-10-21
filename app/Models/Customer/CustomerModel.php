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
    'user',
    'credit_status',
    'date',
    'time'
  ];

  public function quit($id): array
  {
    if (empty($id) || !is_numeric($id)) {
      return [
        'status' => false,
        'msg' => 'ID DE CLIENTE NO VÁLIDO'
      ];
    }

    try {
      $customer = $this->find($id);

      if (!$customer) {
        return [
          'status' => false,
          'msg' => 'NO SE ENCONTRÓ EL CLIENTE'
        ];
      }

      $deleted = $this->delete($id);

      if (!$deleted) {
        return [
          'status' => false,
          'msg' => 'NO SE PUDO ELIMINAR EL CLIENTE'
        ];
      }

      return [
        'status' => true,
        'msg' => 'CLIENTE ELIMINADO CORRECTAMENTE'
      ];

    } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
      log_message(
        'error',
        'No se pudo eliminar el cliente ID ' . $id . ': ' . $e->getMessage()
      );

      return [
        'status' => false,
        'msg' => 'NO SE PUEDE ELIMINAR EL CLIENTE PORQUE TIENE HISTORIAL REGISTRADO'
      ];

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al eliminar cliente ID ' . $id . ': ' . $e->getMessage()
      );

      return [
        'status' => false,
        'msg' => 'OCURRIÓ UN ERROR AL ELIMINAR EL CLIENTE'
      ];
    }
  }

  public function get_ById($id)
  {
    if (empty($id) || !is_numeric($id)) {
      return null;
    }

    return $this->select([
      'customers.id',
      'customers.ci',
      'customers.name',
      'customers.cel',
      'customers.correo',
      'customers.credit_status',
      'customers.user',
      'users.name AS created_by',
      'customers.date',
      'customers.time'
    ])
      ->join(
        'users',
        'users.id = customers.user',
        'left'
      )
      ->where('customers.id', (int) $id)
      ->first();
  }
  protected $errors = [];

  public function search($value)
  {
    return $this->select('id, ci, name')
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

  public function edit(array $values, $id): bool
  {
    $id = (int) $id;

    if ($id <= 0) {
      $this->errors = ['ID DE CLIENTE NO VÁLIDO'];
      return false;
    }

    $ci = trim((string) ($values['ci'] ?? ''));
    $name = trim((string) ($values['name'] ?? ''));
    $cel = trim((string) ($values['cel'] ?? ''));
    $correo = strtolower(trim((string) ($values['correo'] ?? '')));
    $creditStatus = $values['credit_status'] ?? null;

    if ($ci === '' || $name === '' || $cel === '' || $correo === '') {
      $this->errors = ['TODOS LOS CAMPOS SON OBLIGATORIOS'];
      return false;
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
      $this->errors = ['EL CORREO ELECTRÓNICO NO ES VÁLIDO'];
      return false;
    }

    // Verificar que exista el cliente
    $customer = $this->find($id);

    if (!$customer) {
      $this->errors = ['NO SE ENCONTRÓ EL CLIENTE'];
      return false;
    }

    // Verificar cédula duplicada en otro cliente
    $duplicateCi = $this
      ->where('ci', $ci)
      ->where('id !=', $id)
      ->first();

    if ($duplicateCi) {
      $this->errors = ['YA EXISTE OTRO CLIENTE CON ESTA CÉDULA'];
      return false;
    }

    // Verificar correo duplicado en otro cliente
    $duplicateEmail = $this
      ->where('correo', $correo)
      ->where('id !=', $id)
      ->first();

    if ($duplicateEmail) {
      $this->errors = ['YA EXISTE OTRO CLIENTE CON ESTE CORREO'];
      return false;
    }

    $data = [
      'ci' => $ci,
      'name' => $name,
      'cel' => $cel,
      'correo' => $correo,
      'credit_status' => $creditStatus
    ];

    try {
      return $this->update($id, $data);
    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al editar cliente ID ' . $id . ': ' . $e->getMessage()
      );

      $this->errors = ['OCURRIÓ UN ERROR AL ACTUALIZAR EL CLIENTE'];

      return false;
    }
  }

  public function create(array $values): array
  {
    $ci = trim((string) ($values['ci'] ?? ''));
    $name = trim((string) ($values['name'] ?? ''));
    $cel = trim((string) ($values['cel'] ?? ''));
    $correo = strtolower(trim((string) ($values['correo'] ?? '')));
    $creditStatus = $values['credit_status'] ?? null;
    $user = $values['user'] ?? null;

    // Validar campos obligatorios.
    if ($name === '' || $ci === '' || $cel === '' || $correo === '') {
      return [
        'status' => false,
        'msg' => 'TODOS LOS CAMPOS SON OBLIGATORIOS'
      ];
    }

    // Validar cédula duplicada.
    if ($this->where('ci', $ci)->countAllResults() > 0) {
      return [
        'status' => false,
        'msg' => 'YA EXISTE UN CLIENTE CON ESTE RUC | CÉDULA'
      ];
    }

    // Validar correo duplicado.
    if ($this->where('correo', $correo)->countAllResults() > 0) {
      return [
        'status' => false,
        'msg' => 'YA EXISTE UN CLIENTE CON ESTE CORREO'
      ];
    }

    // Preparar datos para insertar.
    $data = [
      'ci' => $ci,
      'name' => $name,
      'cel' => $cel,
      'correo' => $correo,
      'credit_status' => $creditStatus,
      'user' => $user,
      'date' => $values['date'],
      'time' => $values['time']

    ];

    // Insertar cliente.
    try {
      $inserted = $this->insert($data);

      if ($inserted === false) {
        return [
          'status' => false,
          'msg' => 'NO SE PUDO GUARDAR EL CLIENTE',
          'errors' => $this->errors()
        ];
      }

      return [
        'status' => true,
        'msg' => 'CLIENTE GUARDADO CORRECTAMENTE',
        'id' => $this->getInsertID()
      ];

    } catch (\Throwable $e) {
      log_message(
        'error',
        'Error al insertar cliente: ' . $e->getMessage()
      );

      return [
        'status' => false,
        'msg' => 'OCURRIÓ UN ERROR AL GUARDAR EL CLIENTE'
      ];
    }
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
        'data' => $this->find($insert)
      ];

    } catch (\Exception $e) {
      log_message('error', $e->getMessage());
      return ['status' => false, 'message' => 'Error interno del servidor'];
    }
  }

}