<?php
namespace App\Controllers\Config\Sucursals;

use App\Controllers\BaseController;
use App\Models\Point\SucursalsModel;

class Sucursal extends BaseController
{

  public function panel_load()
  {
    $Model = new SucursalsModel();

    $data = [
      'values' => $Model->get_All()
    ];
    $html = view('Config/modules/sucursals/sucursal/home', $data);

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
      'title' => 'Nueva Sucursal',
      'values' => [],
    ];
    $html = view('Config/modules/sucursals/sucursal/form', $data);

    return $this->finish(
      true,
      '',
      $html,
    );

  }

  public function form_new_save()
  {
    // 
    $Model = new SucursalsModel();
    // 
    $values = $this->request->getPost();
    // 
    if (!$Model->add($values['values'])) {
      return $this->finish(
        false,
        'ERROR AL GUARDAR LA IMPRESORA',
        [
          'error' => $Model->errors() ? implode(', ', $Model->errors()) : 'Error al insertar ',
          'values' => $values['values']
        ]
      );
    }
    return $this->finish(
      true,
      'GUARDADO CORRECTAMENTE',
    );

  }
  public function form_edit_open()
  {
    $Model = new SucursalsModel();
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
      'title' => 'Editar Sucursal',
      'values' => $values,

    ];

    $html = view(
      'Config/modules/sucursals/sucursal/form',
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
    $Model = new SucursalsModel();

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
    $Model = new SucursalsModel();

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
