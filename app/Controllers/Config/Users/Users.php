<?php
namespace App\Controllers\Config\Users;

use App\Controllers\BaseController;
use App\Models\Auth\UsersModel;
use App\Models\Auth\UsersCategoryModel;
use App\Models\Auth\StatusModel;


class Users extends BaseController
{

  public function panel_load()
  {
    $model = new UsersModel();

    $data = [
      'values' => $model->get_All()
    ];
    $html = view('Config/modules/users/users/home', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function form_new_open()
  {
    $UsersCategoryModel = new UsersCategoryModel();
    $StatusModel = new StatusModel();
    $data = [
      'type' => 'new',
      'title' => 'Nuevo Usuario',
      'values' => [],
      'category' => $UsersCategoryModel->get_All(),
      'status' => $StatusModel->get_All(),
    ];
    $html = view('Config/modules/users/users/form', $data);

    return $this->finish(
      true,
      '',
      $html,
    );

  }

  public function form_new_save()
  {
    $model = new UsersModel();

    $values = $this->request->getPost();

    if (!isset($values['values']) || !is_array($values['values'])) {

      return $this->finish(
        false,
        'DATOS INVALIDOS'
      );
    }

    $data = $values['values'];

    // Verificar contraseña
    if (empty($data['password'])) {

      return $this->finish(
        false,
        'LA CONTRASEÑA ES OBLIGATORIA'
      );
    }

    // Verificar usuario
    if (empty(trim($data['user'] ?? ''))) {

      return $this->finish(
        false,
        'EL NOMBRE DE USUARIO ES OBLIGATORIO'
      );
    }

    // Encriptar contraseña
    $data['password'] = password_hash(
      $data['password'],
      PASSWORD_DEFAULT
    );

    // Guardar usuario
    if (!$model->add($data)) {

      $errors = $model->getCustomErrors();

      return $this->finish(
        false,
        $errors['user'] ?? 'ERROR AL GUARDAR'
      );
    }

    return $this->finish(
      true,
      'GUARDADO CORRECTAMENTE'
    );
  }
  public function form_edit_open()
  {
    $model = new UsersModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'No se recibió el ID.',
        ''
      );
    }

    // Obtener usuario
    $user = $model->get_ById($id);

    if (!$user) {
      return $this->finish(
        false,
        'No se encontró.',
        ''
      );
    }

    // Modelos de FK
    $categoryModel = new UsersCategoryModel();
    $statusModel = new StatusModel();

    $data = [
      'type' => 'edit',
      'title' => 'Editar Usuario',
      'values' => $user,
      'category' => $categoryModel->findAll(),
      'status' => $statusModel->findAll()
    ];

    $html = view(
      'Config/modules/users/users/form',
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
    $model = new UsersModel();

    $values = $this->request->getPost();

    $value = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (!$model->edit($value, $id)) {
      return $this->finish(
        false,
        'ERROR AL EDITAR',
        [
          'error' => $model->errors()
            ? implode(', ', $model->errors())
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
  public function form_edit_password_save()
  {
    $model = new UsersModel();

    $values = $this->request->getPost();

    $value = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (empty($value['password'])) {
      return $this->finish(
        false,
        'LA CONTRASEÑA ES OBLIGATORIA'
      );
    }

    // Encriptar nueva contraseña
    $password = password_hash(
      $value['password'],
      PASSWORD_DEFAULT
    );

    $data = [
      'password' => $password
    ];

    if (!$model->update($id, $data)) {

      return $this->finish(
        false,
        'ERROR AL ACTUALIZAR LA CONTRASEÑA',
        [
          'error' => $model->errors()
            ? implode(', ', $model->errors())
            : 'ERROR AL ACTUALIZAR LA CONTRASEÑA'
        ]
      );
    }

    return $this->finish(
      true,
      'CONTRASEÑA ACTUALIZADA CORRECTAMENTE'
    );
  }
  public function delete_save()
  {
    $model = new UsersModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID '
      );
    }

    if (!$model->quit($id)) {
      return $this->finish(
        false,
        'ERROR AL ELIMINAR',
        [
          'error' => $model->errors()
            ? implode(', ', $model->errors())
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
