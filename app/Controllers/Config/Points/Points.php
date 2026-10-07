<?php
namespace App\Controllers\Config\Points;

use App\Controllers\BaseController;
use App\Models\Point\ExpeditionPointModel;
use App\Models\Point\SucursalsModel;

class Points extends BaseController
{

  public function panel_load()
  {
    $Model = new ExpeditionPointModel();

    $data = [
      'values' => $Model->get_All()
    ];
    $html = view('Config/modules/expedition_point/point/home', $data);

    return $this->finish(true, '', $html);

  }

  public function form_new_open()
  {
    $sucursals = new SucursalsModel();
    $data = [
      'type' => 'new',
      'title' => 'Nuevo Punto de Expedición',
      'values' => [],
      'sucursals' => $sucursals->get_All()
    ];
    $html = view('Config/modules/expedition_point/point/form', $data);

    return $this->finish(
      true,
      '',
      $html,
    );

  }

  public function form_new_save()
  {
    $model = new ExpeditionPointModel();
    $values = $this->request->getPost();
    $data = $values['values'] ?? [];

    if (empty($data)) {
      return $this->finish(
        false,
        'NO SE RECIBIERON DATOS'
      );
    }

    $code = trim($data['code'] ?? '');
    $sucursal = $data['sucursal'] ?? '';

    if ($code === '') {
      return $this->finish(
        false,
        'EL CODIGO ES OBLIGATORIO'
      );
    }

    if (empty($sucursal)) {
      return $this->finish(
        false,
        'DEBE SELECCIONAR UNA SUCURSAL'
      );
    }

    // Verificar código duplicado en la misma sucursal
    if ($model->exists_code_sucursal($code, $sucursal)) {

      return $this->finish(
        false,
        'EL CODIGO YA EXISTE EN ESTA SUCURSAL'
      );
    }

    unset($data['id']);

    if (!$model->add($data)) {

      return $this->finish(
        false,
        'ERROR AL GUARDAR',
        [
          'error' => $model->errors()
            ? implode(', ', $model->errors())
            : 'ERROR AL INSERTAR PUNTO DE EXPEDICION',

          'values' => $data
        ]
      );
    }

    return $this->finish(
      true,
      'PUNTO DE EXPEDICION GUARDADO CORRECTAMENTE'
    );
  }

  public function form_edit_open()
  {
    $sucursals = new SucursalsModel();
    $Model = new ExpeditionPointModel();
    $values = $this->request->getPost();

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
        'No se encontró.',
        ''
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Punto de Expedición',
      'values' => $values,
      'sucursals' => $sucursals->get_All()
    ];

    $html = view(
      'Config/modules/expedition_point/point/form',
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
    $model = new ExpeditionPointModel();

    $values = $this->request->getPost();

    $data = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    $code = trim($data['code'] ?? '');
    $sucursal = $data['sucursal'] ?? '';

    if ($code === '') {
      return $this->finish(
        false,
        'EL CODIGO ES OBLIGATORIO'
      );
    }

    if (empty($sucursal)) {
      return $this->finish(
        false,
        'DEBE SELECCIONAR UNA SUCURSAL'
      );
    }

    /*
     * Verificar si ya existe el mismo
     * código en la misma sucursal.
     *
     * Se excluye el ID que estamos editando.
     */
    if ($model->exists_code_sucursal($code, $sucursal, $id)) {
      return $this->finish(
        false,
        'EL CODIGO YA EXISTE EN ESTA SUCURSAL'
      );
    }

    // No permitir modificar el ID
    unset($data['id']);

    if (!$model->edit($data, $id)) {

      return $this->finish(
        false,
        'ERROR AL EDITAR',
        [
          'error' => $model->errors()
            ? implode(', ', $model->errors())
            : 'ERROR AL EDITAR',

          'values' => $data
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
    $model = new ExpeditionPointModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {

      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (!$model->quit($id)) {

      return $this->finish(
        false,
        $model->get_delete_error() ?: 'ERROR AL ELIMINAR'
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
