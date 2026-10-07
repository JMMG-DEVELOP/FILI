<?php
namespace App\Controllers\Config\Printers;

use App\Controllers\BaseController;
use App\Models\Printers\PrintersDriversModel;




class Drivers extends BaseController
{

  public function panel_load()
  {
    $PrintersDriversModel = new PrintersDriversModel();

    $data = [
      'values' => $PrintersDriversModel->get_All()
    ];
    $html = view('Config/modules/printers/drivers/home', $data);

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
      'title' => 'Nuevo Driver',
      'values' => [],
    ];
    $html = view('Config/modules/printers/drivers/form', $data);

    return $this->finish(
      true,
      '',
      $html,
    );

  }

  public function form_new_save()
  {
    // 
    $PrintersDriversModel = new PrintersDriversModel();
    // 
    $values = $this->request->getPost();
    // 
    if (!$PrintersDriversModel->add($values['values'])) {
      return $this->finish(
        false,
        'ERROR AL GUARDAR LA IMPRESORA',
        [
          'error' => $PrintersDriversModel->errors() ? implode(', ', $PrintersDriversModel->errors()) : 'Error al insertar producto',
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
    $PrintersDriversModel = new PrintersDriversModel();
    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'No se recibió el ID.',
        ''
      );
    }

    $values = $PrintersDriversModel->get_ById($id);

    if (!$values) {
      return $this->finish(
        false,
        'No se encontró.',
        ''
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Impresora',
      'values' => $values,

    ];

    $html = view(
      'Config/modules/printers/drivers/form',
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
    $PrintersDriversModel = new PrintersDriversModel();

    $values = $this->request->getPost();

    $value = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (!$PrintersDriversModel->edit($value, $id)) {
      return $this->finish(
        false,
        'ERROR AL EDITAR',
        [
          'error' => $PrintersDriversModel->errors()
            ? implode(', ', $PrintersDriversModel->errors())
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
    $PrintersDriversModel = new PrintersDriversModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID '
      );
    }

    if (!$PrintersDriversModel->quit($id)) {
      return $this->finish(
        false,
        'ERROR AL ELIMINAR',
        [
          'error' => $PrintersDriversModel->errors()
            ? implode(', ', $PrintersDriversModel->errors())
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
