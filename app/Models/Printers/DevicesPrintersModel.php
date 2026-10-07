<?php

namespace App\Models\Printers;

use CodeIgniter\Model;

class DevicesPrintersModel extends Model
{
  protected $table = 'devices_printers';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'device',
    'printer',
    'type'

  ];
  public function quit($id)
  {
    return $this
      ->where($this->primaryKey, $id)
      ->delete();
  }
  public function edit($values, $id)
  {
    return $this
      ->where($this->primaryKey, $id)
      ->set($values)
      ->update();
  }
  public function get_all()
  {
    return $this
      ->select([
        'devices_printers.id',
        'devices_printers.device',
        'devices_printers.printer',
        'devices_printers.type',

        // TYPE
        'document_type.name AS type_name',

        // DEVICE
        'devices.uuid AS device_uuid',
        'devices.name AS device_name',
        'devices.type AS device_type',
        'devices.enabled AS device_enabled',

        // PRINTER
        'printers.name AS printer_name',
        'printers.system_name AS printer_system_name',
        'printers.auto_cut AS printer_auto_cut',
        'printers.driver AS printer_driver',
        'printers.paper AS printer_paper',
        'printers.charset AS printer_charset',
      ])
      ->join(
        'document_type',
        'document_type.id = devices_printers.type',
        'left'
      )
      ->join(
        'devices',
        'devices.id = devices_printers.device',
        'left'
      )
      ->join(
        'printers',
        'printers.id = devices_printers.printer',
        'left'
      )
      ->findAll();
  }

}