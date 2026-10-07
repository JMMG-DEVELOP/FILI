<?php

namespace App\Models\Point;

use CodeIgniter\Model;

class InvoiceSequenceModel extends Model
{

  protected $table = 'invoice_sequence';

  protected $primaryKey = 'id';

  protected $returnType = 'array';

  protected $allowedFields = [
    'last_number',
    'expedition_point'
  ];
  protected $deleteError = '';

  public function get_delete_error()
  {
    return $this->deleteError;
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

        $this->deleteError = 'NO SE PUDO ELIMINAR LA SECUENCIA';

        return false;
      }

      return true;

    } catch (\Throwable $e) {

      $this->deleteError = $e->getMessage();

      if (
        str_contains(
          $e->getMessage(),
          'foreign key constraint fails'
        )
      ) {

        $this->deleteError =
          'NO SE PUEDE ELIMINAR LA SECUENCIA PORQUE ESTA SIENDO UTILIZADA';

      }

      return false;
    }
  }
  public function get_all()
  {
    return $this
      ->select([
        'invoice_sequence.id',
        'invoice_sequence.last_number',
        'invoice_sequence.expedition_point',
        'expedition_point.code AS expedition_point_code',
        'expedition_point.sucursal AS sucursal_id',
        'sucursals.name AS sucursal_name'
      ])
      ->join(
        'expedition_point',
        'expedition_point.id = invoice_sequence.expedition_point',
        'left'
      )
      ->join(
        'sucursals',
        'sucursals.id = expedition_point.sucursal',
        'left'
      )
      ->orderBy(
        'expedition_point.sucursal',
        'ASC'
      )
      ->orderBy(
        'expedition_point.code',
        'ASC'
      )
      ->findAll();
  }

  public function get_by_id($id)
  {
    if (empty($id)) {
      return false;
    }

    return $this
      ->select([
        'invoice_sequence.id',
        'invoice_sequence.last_number',
        'invoice_sequence.expedition_point',
        'expedition_point.code AS expedition_point_code',
        'expedition_point.sucursal AS sucursal_id',
        'sucursals.name AS sucursal_name'
      ])
      ->join(
        'expedition_point',
        'expedition_point.id = invoice_sequence.expedition_point',
        'left'
      )
      ->join(
        'sucursals',
        'sucursals.id = expedition_point.sucursal',
        'left'
      )
      ->where(
        'invoice_sequence.id',
        $id
      )
      ->first();
  }

  public function edit($values, $id)
  {
    if (empty($id) || empty($values)) {
      return false;
    }

    return $this
      ->where(
        $this->primaryKey,
        $id
      )
      ->set($values)
      ->update();
  }

}