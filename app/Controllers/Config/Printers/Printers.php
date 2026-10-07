<?php
namespace App\Controllers\Config\Printers;

use App\Controllers\BaseController;

use App\Models\Printers\PrintersModel;
use App\Models\Printers\PrintersPaperModel;
use App\Models\Printers\PrintersDriversModel;
use App\Models\Printers\PrintersCharsetsModel;




class Printers extends BaseController
{

  public function panel_load()
  {
    $PrintersModel = new PrintersModel();

    $data = [
      'values' => $PrintersModel->get_Printers()
    ];
    $html = view('Config/modules/printers/printers/home', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function form_new_open()
  {
    $PrintersPaperModel = new PrintersPaperModel();
    $PrintersDriversModel = new PrintersDriversModel();
    $PrintersCharsetsModel = new PrintersCharsetsModel();

    $data = [
      'type' => 'new',
      'title' => 'Nueva Impresora',
      'values' => [],
      'paper' => $PrintersPaperModel->get_PrintersPaper(),
      'drivers' => $PrintersDriversModel->get_All(),
      'charset' => $PrintersCharsetsModel->get_all(),


    ];
    $html = view('Config/modules/printers/printers/form', $data);

    return $this->finish(
      true,
      '',
      $html,
    );

  }

  public function form_new_save()
  {
    // 
    $PrintersModel = new PrintersModel();
    // 
    $values = $this->request->getPost();
    // 
    if (!$PrintersModel->add($values['printers'])) {
      return $this->finish(
        false,
        'ERROR AL GUARDAR LA IMPRESORA',
        [
          'error' => $PrintersModel->errors() ? implode(', ', $PrintersModel->errors()) : 'Error al insertar producto',
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
    $PrintersModel = new PrintersModel();
    $PrintersPaperModel = new PrintersPaperModel();
    $PrintersDriversModel = new PrintersDriversModel();
    $PrintersCharsetsModel = new PrintersCharsetsModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'No se recibió el ID de la impresora.',
        ''
      );
    }

    $printer = $PrintersModel->get_PrintersById($id);

    if (!$printer) {
      return $this->finish(
        false,
        'No se encontró la impresora.',
        ''
      );
    }

    $data = [
      'type' => 'edit',
      'title' => 'Editar Impresora',
      'values' => $printer,
      'paper' => $PrintersPaperModel->get_PrintersPaper(),
      'drivers' => $PrintersDriversModel->get_All(),
      'charset' => $PrintersCharsetsModel->get_all(),
    ];

    $html = view(
      'Config/modules/printers/printers/form',
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
    $PrintersModel = new PrintersModel();

    $values = $this->request->getPost();

    $value = $values['values'] ?? [];
    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (!$PrintersModel->edit($value, $id)) {
      return $this->finish(
        false,
        'ERROR AL EDITAR',
        [
          'error' => $PrintersModel->errors()
            ? implode(', ', $PrintersModel->errors())
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
    $PrintersModel = new PrintersModel();

    $values = $this->request->getPost();

    $id = $values['id'] ?? null;

    if (!$id) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID '
      );
    }

    if (!$PrintersModel->quit($id)) {
      return $this->finish(
        false,
        'ERROR AL ELIMINAR',
        [
          'error' => $PrintersModel->errors()
            ? implode(', ', $PrintersModel->errors())
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
