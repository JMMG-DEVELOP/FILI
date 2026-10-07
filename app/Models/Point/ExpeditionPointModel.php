<?php

namespace App\Models\Point;

use CodeIgniter\Model;

class ExpeditionPointModel extends Model
{
  protected $deleteError = '';
  protected $table = 'expedition_point';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'code',
    'sucursal',
  ];

  public function get_all()
  {
    return $this
      ->select([
        'expedition_point.id',
        'expedition_point.code',
        'expedition_point.sucursal',

        // SUCURSAL
        'sucursals.name AS sucursal_name',
        'sucursals.id AS sucursal_id',

      ])
      ->join(
        'sucursals',
        'sucursals.id = expedition_point.sucursal',
        'left'
      )
      ->findAll();
  }

  public function get_ById($id)
  {
    if (empty($id)) {
      return null;
    }

    return $this
      ->select([
        'expedition_point.id',
        'expedition_point.code',
        'expedition_point.sucursal',

        // SUCURSAL
        'sucursals.name AS sucursal_name',
      ])
      ->join(
        'sucursals',
        'sucursals.id = expedition_point.sucursal',
        'left'
      )
      ->where(
        'expedition_point.id',
        $id
      )
      ->first();
  }

  public function add($values)
  {
    if (empty($values)) {
      return false;
    }

    $this->db->transStart();

    $id = $this->insert($values, true);

    if (!$id) {
      $this->db->transRollback();
      return false;
    }

    $this->db->table('invoice_sequence')->insert([
      'last_number' => '0000001',
      'expedition_point' => $id
    ]);

    if ($this->db->affectedRows() <= 0) {
      $this->db->transRollback();
      return false;
    }

    $this->db->table('document_sequence')->insert([
      'last_number' => '0000001',
      'expedition_point' => $id
    ]);

    if ($this->db->affectedRows() <= 0) {
      $this->db->transRollback();
      return false;
    }

    $this->db->transComplete();

    if ($this->db->transStatus() === false) {
      return false;
    }

    return $id;
  }
  public function exists_code_sucursal($code, $sucursal, $id = null)
  {
    if (empty($code) || empty($sucursal)) {
      return false;
    }

    $builder = $this
      ->where('code', $code)
      ->where('sucursal', $sucursal);

    if (!empty($id)) {
      $builder->where('id !=', $id);
    }

    return $builder->countAllResults() > 0;
  }

  public function edit($values, $id)
  {
    if (empty($id) || empty($values)) {
      return false;
    }

    return $this
      ->where($this->primaryKey, $id)
      ->set($values)
      ->update();
  }

  public function quit($id)
  {
    $this->deleteError = '';

    if (empty($id)) {
      $this->deleteError = 'NO SE RECIBIO EL ID';
      return false;
    }

    try {

      $result = $this->delete($id);

      if (!$result) {

        $this->deleteError = 'NO SE PUDO ELIMINAR EL REGISTRO';

        return false;
      }

      return true;

    } catch (\Throwable $e) {

      log_message(
        'error',
        'ERROR AL ELIMINAR PUNTO DE EXPEDICION: ' .
        $e->getMessage()
      );

      // Error de FK
      if (
        str_contains(
          strtolower($e->getMessage()),
          'foreign key'
        )
      ) {

        $this->deleteError =
          'NO SE PUEDE ELIMINAR EL PUNTO DE EXPEDICION PORQUE ESTA SIENDO UTILIZADO';

      } else {

        $this->deleteError =
          'NO SE PUDO ELIMINAR EL PUNTO DE EXPEDICION';
      }

      return false;
    }
  }

  public function get_delete_error()
  {
    return $this->deleteError;
  }
}