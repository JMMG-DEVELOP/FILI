<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class PointDeviceModel extends Model
{
  protected $table = 'point_device';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'device',
    'expedition_point'
  ];
  protected $errors = [];
  public function get_Devices()
  {
    return $this->db->table('devices')
      ->select('
      id,
      uuid,
      name,
      type,
      enabled
    ')
      ->where('enabled', 1)
      ->orderBy('name', 'ASC')
      ->get()
      ->getResultArray();
  }
  public function get_Expedition_Sucursals()
  {
    return $this->db->table('expedition_point ep')
      ->select('
        ep.id,
        ep.code,
        ep.sucursal,
        s.name as sucursal_name
      ')
      ->join(
        'sucursals s',
        's.id = ep.sucursal'
      )
      ->orderBy('ep.sucursal', 'ASC')
      ->orderBy('ep.code', 'ASC')
      ->get()
      ->getResultArray();
  }

  public function get_by_id($id)
  {
    if (empty($id)) {
      return null;
    }

    return $this->db->table('point_device pd')
      ->select('
        pd.id,
        pd.device,
        pd.expedition_point,

        d.name as device_name,
        d.uuid as device_uuid,

        ep.code as expedition_point_code,
        ep.sucursal,

        s.name as sucursal_name
      ')
      ->join(
        'devices d',
        'd.id = pd.device'
      )
      ->join(
        'expedition_point ep',
        'ep.id = pd.expedition_point'
      )
      ->join(
        'sucursals s',
        's.id = ep.sucursal'
      )
      ->where('pd.id', $id)
      ->get()
      ->getRowArray();
  }

  public function exists_assignment($device, $expeditionPoint, $id = null)
  {
    $builder = $this->db->table($this->table);

    $builder
      ->where('device', $device)
      ->where('expedition_point', $expeditionPoint);

    if (!empty($id)) {
      $builder->where('id !=', $id);
    }

    return $builder
      ->countAllResults() > 0;
  }

  public function add($values)
  {
    if (empty($values)) {
      return false;
    }

    $device = $values['device'] ?? null;
    $expeditionPoint = $values['expedition_point'] ?? null;

    if (empty($device) || empty($expeditionPoint)) {
      return false;
    }

    // Validar si la asignación ya existe
    $exists = $this->db->table($this->table)
      ->where('device', $device)
      ->where('expedition_point', $expeditionPoint)
      ->countAllResults();

    if ($exists > 0) {
      $this->errors = [
        'device' => 'ESTE DISPOSITIVO YA ESTÁ ASIGNADO A ESTE PUNTO DE EXPEDICIÓN'
      ];

      return false;
    }

    // Guardar la asignación
    return $this->insert([
      'device' => $device,
      'expedition_point' => $expeditionPoint
    ]);
  }

  public function edit($values, $id)
  {
    if (empty($values) || empty($id)) {
      return false;
    }

    $device = $values['device'] ?? null;
    $expeditionPoint = $values['expedition_point'] ?? null;

    if (empty($device) || empty($expeditionPoint)) {
      return false;
    }

    // Validar si otro registro ya tiene esta asignación
    $exists = $this->db->table($this->table)
      ->where('device', $device)
      ->where('expedition_point', $expeditionPoint)
      ->where('id !=', $id)
      ->countAllResults();

    if ($exists > 0) {
      $this->errors = [
        'device' => 'ESTE DISPOSITIVO YA TIENE ASIGNADO EL MISMO PUNTO DE EXPEDICIÓN'
      ];

      return false;
    }

    // Actualizar la asignación
    return $this->update($id, [
      'device' => $device,
      'expedition_point' => $expeditionPoint
    ]);
  }

  public function quit($id)
  {
    if (empty($id)) {
      return false;
    }

    try {

      $deleted = $this->delete($id);

      if (!$deleted) {
        $this->errors = [
          'delete' => 'NO SE PUDO ELIMINAR LA ASIGNACIÓN'
        ];

        return false;
      }

      return true;

    } catch (\Throwable $e) {

      $message = $e->getMessage();

      if (
        stripos($message, 'foreign key constraint fails') !== false ||
        stripos($message, 'Cannot delete or update a parent row') !== false
      ) {
        $this->errors = [
          'delete' => 'NO SE PUEDE ELIMINAR LA ASIGNACIÓN PORQUE ESTÁ SIENDO UTILIZADA'
        ];

        return false;
      }

      $this->errors = [
        'delete' => 'ERROR AL ELIMINAR LA ASIGNACIÓN'
      ];

      return false;
    }
  }

  public function getAll()
  {
    return $this->db->table('point_device pd')
      ->select('
        pd.id,
        pd.device,
        pd.expedition_point,

        d.name as device_name,
        d.uuid as device_uuid,

        ep.id as expedition_point_id,
        ep.code as expedition_point_code,
        ep.sucursal as sucursal_id,

        s.name as sucursal_name,

        ds.id as document_sequence_id,
        ds.last_number as document_last_number,

        isq.id as invoice_sequence_id,
        isq.last_number as invoice_last_number
      ')
      ->join(
        'devices d',
        'd.id = pd.device'
      )
      ->join(
        'expedition_point ep',
        'ep.id = pd.expedition_point'
      )
      ->join(
        'sucursals s',
        's.id = ep.sucursal'
      )
      ->join(
        'document_sequence ds',
        'ds.expedition_point = ep.id',
        'left'
      )
      ->join(
        'invoice_sequence isq',
        'isq.expedition_point = ep.id',
        'left'
      )
      ->orderBy('ep.sucursal', 'ASC')
      ->orderBy('d.name', 'ASC')
      ->orderBy('ep.code', 'ASC')
      ->get()
      ->getResultArray();
  }

  public function getDeviceExpedition($deviceId)
  {
    if (empty($deviceId)) {
      return [];
    }

    $rows = $this->db->table('point_device pd')
      ->select('
      ep.sucursal,
      ep.id as expedition_point,
      ep.code,

      ds.id as document_sequence_id,
      ds.last_number as document_last_number,

      isq.id as invoice_sequence_id,
      isq.last_number as invoice_last_number
    ')
      ->join(
        'expedition_point ep',
        'ep.id = pd.expedition_point'
      )
      ->join(
        'document_sequence ds',
        'ds.expedition_point = ep.id',
        'left'
      )
      ->join(
        'invoice_sequence isq',
        'isq.expedition_point = ep.id',
        'left'
      )
      ->where('pd.device', $deviceId)
      ->orderBy('ep.sucursal', 'ASC')
      ->orderBy('ep.code', 'ASC')
      ->get()
      ->getResultArray();

    if (empty($rows)) {
      return [];
    }

    $values = [];

    foreach ($rows as $row) {
      $values[] = [
        'sucursal' => $row['sucursal'],

        'expedition_point' => $row['expedition_point'],

        'expedition_code' => $row['code'],

        'document_sequence_id' => $row['document_sequence_id'],

        'document_last_number' => $row['document_last_number'],

        'invoice_sequence_id' => $row['invoice_sequence_id'],

        'invoice_last_number' => $row['invoice_last_number'],

        'document_number' =>
          str_pad(
            $row['sucursal'],
            3,
            '0',
            STR_PAD_LEFT
          ) . ' ' .
          str_pad(
            $row['code'],
            3,
            '0',
            STR_PAD_LEFT
          ) . ' ' .
          str_pad(
            $row['document_last_number'],
            7,
            '0',
            STR_PAD_LEFT
          ),

        'invoice_number' =>
          str_pad(
            $row['sucursal'],
            3,
            '0',
            STR_PAD_LEFT
          ) . ' ' .
          str_pad(
            $row['code'],
            3,
            '0',
            STR_PAD_LEFT
          ) . ' ' .
          str_pad(
            $row['invoice_last_number'],
            7,
            '0',
            STR_PAD_LEFT
          )
      ];
    }

    return $values;
  }


}