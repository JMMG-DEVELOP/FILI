<?php
namespace App\Controllers\Config\Users;

use App\Controllers\BaseController;
use App\Models\Users\UsersCategoryModel;


class Category extends BaseController
{

  public function panel_load()
  {
    $model = new UsersCategoryModel();

    $data = [
      'values' => $model->get_All()
    ];
    $html = view('Config/modules/users/category/home', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function form_new_open()
  {
    $data = [
      'type' => 'new',
      'title' => 'Nueva Categoria',
      'values' => [],
    ];
    $html = view('Config/modules/users/category/form', $data);

    return $this->finish(
      true,
      '',
      $html,
    );

  }

  public function form_new_save()
  {
    // 
    $model = new UsersCategoryModel();
    // 
    $values = $this->request->getPost();
    // 
    if (!$model->add($values['values'])) {
      return $this->finish(
        false,
        'ERROR AL GUARDAR LA IMPRESORA',
        [
          'error' => $model->errors() ? implode(', ', $model->errors()) : 'Error al insertar producto',
          'values' => $values['printers']
        ]
      );
    }
    return $this->finish(
      true,
      'IMPRESORA GUARDADO CORRECTAMENTE',
    );

  }
  public function form_edit_open()
  {
    $model = new UsersCategoryModel();
    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'No se recibió el ID.',
        ''
      );
    }

    $values = $model->get_ById($id);

    if (!$values) {
      return $this->finish(
        false,
        'No se encontró.',
        ''
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Categoria',
      'values' => $values,

    ];

    $html = view(
      'Config/modules/users/category/form',
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
    $model = new UsersCategoryModel();

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

  public function delete_save()
  {
    $model = new UsersCategoryModel();

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
