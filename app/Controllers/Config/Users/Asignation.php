<?php
namespace App\Controllers\Config\Users;

use App\Controllers\BaseController;
use App\Models\Auth\UsersSucursals;

class Asignation extends BaseController
{

  public function panel_load()
  {
    $Model = new UsersSucursals();

    $data = [
      'values' => $Model->getAll()
    ];

    $html = view(
      'Config/modules/users/asignation/home',
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
    $Model = new UsersSucursals();

    $data = [
      'type' => 'new',
      'title' => 'Nueva Asignación de Usuario a Sucursal',
      'values' => [],
      'users' => $Model->get_Users(),
      'sucursals' => $Model->get_Sucursals()
    ];

    $html = view(
      'Config/modules/users/asignation/form',
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
    $Model = new UsersSucursals();

    $values = $this->request->getPost();
    $data = $values['values'] ?? [];

    if (!$Model->add($data)) {
      $errors = $Model->errors();

      $msg = !empty($errors['user'])
        ? $errors['user']
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
    $Model = new UsersSucursals();

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
      'title' => 'Editar Asignación de Usuario',
      'values' => $values,
      'users' => $Model->get_Users(),
      'sucursals' => $Model->get_Sucursals()
    ];

    $html = view(
      'Config/modules/users/asignation/form',
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
    $Model = new UsersSucursals();

    $post = $this->request->getPost();

    $values = $post['values'] ?? [];

    $id = $post['id'] ?? null;

    if (empty($id)) {
      return $this->finish(
        false,
        'NO SE RECIBIÓ EL ID DE LA ASIGNACIÓN'
      );
    }

    if (!$Model->edit($values, $id)) {
      $errors = $Model->errors();

      $msg = !empty($errors['user'])
        ? $errors['user']
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
    $Model = new UsersSucursals();

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
