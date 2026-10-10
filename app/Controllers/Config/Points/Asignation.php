<?php
namespace App\Controllers\Config\Points;

use App\Controllers\BaseController;
use App\Models\Box\PointDeviceModel;

class Asignation extends BaseController
{

  public function panel_load()
  {
    $Model = new PointDeviceModel();

    $data = [
      'values' => $Model->getAll()
    ];

    $html = view(
      'Config/modules/expedition_point/asignation/home',
      $data
    );

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function form_new_open()
  {
    $Model = new PointDeviceModel();

    $data = [
      'type' => 'new',
      'title' => 'Nueva Asignación de Dispositivo a Punto de Expedición',
      'values' => [],
      'device' => $Model->get_Devices(),
      'expedition_point' => $Model->get_Expedition_Sucursals()
    ];

    $html = view(
      'Config/modules/expedition_point/asignation/form',
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
    $Model = new PointDeviceModel();

    $values = $this->request->getPost();
    $data = $values['values'] ?? [];

    if (!$Model->add($data)) {
      $errors = $Model->errors();

      $msg = !empty($errors['device'])
        ? $errors['device']
        : 'ERROR AL GUARDAR LA ASIGNACIÓN';

      return $this->finish(
        false,
        $msg,
        [
          'error' => $msg,
          'values' => $data
        ]
      );
    }

    return $this->finish(
      true,
      'ASIGNACIÓN GUARDADA CORRECTAMENTE'
    );
  }
  public function form_edit_open()
  {
    $Model = new PointDeviceModel();

    $post = $this->request->getPost();
    $id = $post['id'] ?? null;

    if (empty($id)) {
      return $this->finish(
        false,
        'NO SE RECIBIÓ EL ID.',
        ''
      );
    }

    $values = $Model->get_by_id($id);

    if (!$values) {
      return $this->finish(
        false,
        'NO SE ENCONTRÓ LA ASIGNACIÓN.',
        ''
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Asignación de Dispositivo',
      'values' => $values,
      'device' => $Model->get_Devices(),
      'expedition_point' => $Model->get_Expedition_Sucursals()
    ];

    $html = view(
      'Config/modules/expedition_point/asignation/form',
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
    $Model = new PointDeviceModel();

    $post = $this->request->getPost();

    $values = $post['values'] ?? [];
    $id = $post['id'] ?? $values['point_asignation_id'] ?? null;

    if (empty($id)) {
      return $this->finish(
        false,
        'NO SE RECIBIÓ EL ID DE LA ASIGNACIÓN'
      );
    }

    // Validar y actualizar
    if (!$Model->edit($values, $id)) {
      $errors = $Model->errors();

      $msg = !empty($errors['device'])
        ? $errors['device']
        : 'ERROR AL EDITAR LA ASIGNACIÓN';

      return $this->finish(
        false,
        $msg,
        [
          'error' => $msg,
          'values' => $values
        ]
      );
    }

    return $this->finish(
      true,
      'ASIGNACIÓN EDITADA CORRECTAMENTE'
    );
  }
  public function delete_save()
  {
    $Model = new PointDeviceModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIÓ EL ID'
      );
    }

    if (!$Model->quit($id)) {

      $errors = $Model->errors();

      $msg = !empty($errors['delete'])
        ? $errors['delete']
        : 'ERROR AL ELIMINAR';

      return $this->finish(
        false,
        $msg,
        [
          'error' => $msg,
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
