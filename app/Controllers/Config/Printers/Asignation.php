<?php
namespace App\Controllers\Config\Printers;

use App\Controllers\BaseController;

use App\Models\Printers\DevicesPrintersModel;
use App\Models\Printers\PrintersModel;
use App\Models\Auth\DevicesModel;
use App\Models\Point\DocumentTypeModel;



class Asignation extends BaseController
{

  public function panel_load()
  {
    $Model = new DevicesPrintersModel();

    $data = [
      'values' => $Model->get_all()
    ];
    $html = view('Config/modules/printers/asignation/home', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function form_new_open()
  {
    $Model = new DevicesPrintersModel();
    $types = new DocumentTypeModel();
    $devices = new DevicesModel();
    $printers = new PrintersModel();

    $data = [
      'type' => 'new',
      'title' => 'Nueva Asignacion de Impresora',
      'values' => [],

      'printers' => $printers->get_Printers(),
      'devices' => $devices->get_All(),
      'types' => $types->get_all(),
    ];

    $html = view(
      'Config/modules/printers/asignation/form',
      $data
    );

    return $this->finish(
      true,
      '',
      $html
    );
  }

  public function form_new_save()
  {
    $Model = new DevicesPrintersModel();
    $types = new DocumentTypeModel();
    $devices = new DevicesModel();
    $printers = new PrintersModel();

    $values = $this->request->getPost();

    $data = $values['values'] ?? [];

    if (empty($data)) {
      return $this->finish(
        false,
        'NO SE RECIBIERON DATOS'
      );
    }

    if (!$Model->insert($data)) {
      return $this->finish(
        false,
        'ERROR AL GUARDAR',
        [
          'error' => $Model->errors()
            ? implode(', ', $Model->errors())
            : 'Error al insertar',
          'values' => $data
        ]
      );
    }

    return $this->finish(
      true,
      'ASIGNACION GUARDADA CORRECTAMENTE'
    );
  }
  public function form_edit_open()
  {
    $Model = new DevicesPrintersModel();
    $types = new DocumentTypeModel();
    $devices = new DevicesModel();
    $printers = new PrintersModel();

    $id = $this->request->getPost('id');

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    $values = $Model->find($id);

    if (!$values) {
      return $this->finish(
        false,
        'NO SE ENCONTRO LA ASIGNACION'
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Asignacion de Impresora',

      'values' => $values,

      'printers' => $printers->get_Printers(),
      'devices' => $devices->get_All(),
      'types' => $types->get_all(),
    ];

    $html = view(
      'Config/modules/printers/asignation/form',
      $data
    );

    return $this->finish(
      true,
      '',
      $html
    );
  }

  public function form_edit_save()
  {
    $Model = new DevicesPrintersModel();

    $values = $this->request->getPost();

    $value = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (empty($value)) {
      return $this->finish(
        false,
        'NO SE RECIBIERON DATOS'
      );
    }

    if (!$Model->edit($value, $id)) {
      return $this->finish(
        false,
        'ERROR AL EDITAR',
        [
          'error' => $Model->errors()
            ? implode(', ', $Model->errors())
            : 'ERROR AL EDITAR',
          'values' => $value
        ]
      );
    }

    return $this->finish(
      true,
      'EDITADO CORRECTAMENTE'
    );
  }
  public function delete_save()
  {
    $Model = new DevicesPrintersModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID '
      );
    }

    if (!$Model->quit($id)) {
      return $this->finish(
        false,
        'ERROR AL ELIMINAR',
        [
          'error' => $Model->errors()
            ? implode(', ', $Model->errors())
            : 'ERROR AL ELIMINAT',
          'values' => $values
        ]
      );
    }

    return $this->finish(
      true,
      'ELIMINADO CORRECTAMENTE'
    );
  }

  public function finish($status, $msg, $html = '', $data = '')
  {
    return $this->response->setJSON([
      'status' => $status,
      'msg' => $msg,
      'html' => $html,
      'data' => $data,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

}
