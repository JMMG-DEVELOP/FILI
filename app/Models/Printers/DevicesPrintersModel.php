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
    'type',
    'heigth'

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


  public function getPrinterForDevice(int $deviceId, int $documentType): ?array
  {
    if ($deviceId <= 0 || $documentType <= 0) {
      return null;
    }

    return $this->db->table('devices_printers dp')
      ->select([
        'p.dpi AS printer_dpi',
        'dp.id AS device_printer_id',
        'dp.device',
        'dp.type AS document_type',
        'd.name AS device_name',
        'd.enabled AS device_enabled',
        'p.id AS printer_id',
        'p.name AS printer_name',
        'p.system_name AS printer_system_name',
        'p.auto_cut AS printer_auto_cut',
        'p.driver AS printer_driver',
        'pd.name AS driver_name',
        'p.paper AS printer_paper',
        'pp.name AS paper_name',
        'p.charset AS printer_charset',
        'pc.name AS charset_name',
        'pc.code AS charset_code',
        'pc.description AS charset_description',
        'pp.printable AS printable',
        'pp.heigth AS height'

      ])
      ->join('devices d', 'd.id = dp.device')
      ->join('printers p', 'p.id = dp.printer')
      ->join('printers_drivers pd', 'pd.id = p.driver', 'left')
      ->join('printers_paper pp', 'pp.id = p.paper', 'left')
      ->join('printers_charsets pc', 'pc.id = p.charset', 'left')
      ->where('dp.device', $deviceId)
      ->where('dp.type', $documentType)
      ->where('d.enabled', 1)
      ->get()
      ->getRowArray() ?: null;
  }


}