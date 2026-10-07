<?php
namespace App\Controllers\Config\Printers;

use App\Controllers\BaseController;
use App\Models\Auth\DevicesModel;
use App\Models\Auth\StatusModel;

class Devices extends BaseController
{

  public function panel_load()
  {
    $Model = new DevicesModel();

    $data = [
      'values' => $Model->get_All()
    ];
    $html = view('Config/modules/printers/devices/home', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function form_edit_open()
  {
    $Model = new DevicesModel();
    $values = $this->request->getPost();
    $statusModel = new StatusModel();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'No se recibió el ID.',
        ''
      );
    }

    $values = $Model->get_ById($id);

    if (!$values) {
      return $this->finish(
        false,
        'No se encontró ID.',
        ''
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Dispositivo',
      'values' => $values,
      'status' => $statusModel->findAll()

    ];

    $html = view(
      'Config/modules/printers/devices/form',
      $data
    );

    return $this->finish(
      true,
      '',
      $html,
      $values
    );
  }

  public function form_edit_save()
  {
    $Model = new DevicesModel();

    $values = $this->request->getPost();

    $value = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
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
          'values' => $values
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
    $Model = new DevicesModel();

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
            : 'ERROR AL ELIMINAR',
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
