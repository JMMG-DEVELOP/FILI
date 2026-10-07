<?php
namespace App\Controllers\Config\Points;

use App\Controllers\BaseController;
use App\Models\Point\ExpeditionPointModel;
use App\Models\Point\SucursalsModel;
use App\Models\Point\DocumentSequenceModel;
use App\Models\Point\InvoiceSequenceModel;


class Sequence extends BaseController
{

  public function panel_load()
  {
    $Model = new ExpeditionPointModel();
    $DocumentSequenceModel = new DocumentSequenceModel();
    $InvoiceSequenceModel = new InvoiceSequenceModel();

    $data = [
      'document' => $DocumentSequenceModel->get_All(),
      'invoice' => $InvoiceSequenceModel->get_All(),
      'values' => $Model->get_All()
    ];
    $html = view('Config/modules/expedition_point/sequence/home', $data);

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
    $values = $this->request->getPost();

    $id = $values['id'] ?? null;
    $type = $values['type'] ?? null;

    if (empty($id)) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (empty($type)) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL TIPO DE SECUENCIA'
      );
    }

    if ($type === 'invoice') {

      $Model = new InvoiceSequenceModel();

      $title = 'Editar Secuencia de Factura';

    } elseif ($type === 'document') {

      $Model = new DocumentSequenceModel();

      $title = 'Editar Secuencia Interna';

    } else {

      return $this->finish(
        false,
        'TIPO DE SECUENCIA NO VALIDO'
      );
    }

    $sequence = $Model->get_by_id($id);

    if (empty($sequence)) {
      return $this->finish(
        false,
        'NO SE ENCONTRO LA SECUENCIA'
      );
    }

    $data = [
      'title' => $title,
      'type' => $type,
      'values' => $sequence
    ];

    $html = view(
      'Config/modules/expedition_point/sequence/form',
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
    $values = $this->request->getPost();

    $data = $values['values'] ?? [];

    $id = $values['id'] ?? null;

    $type = $values['type'] ?? null;

    if (empty($id)) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (empty($type)) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL TIPO DE SECUENCIA'
      );
    }

    if (empty($data)) {
      return $this->finish(
        false,
        'NO SE RECIBIERON LOS DATOS'
      );
    }

    if (!isset($data['last_number'])) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL NUMERO'
      );
    }

    $update = [
      'last_number' => $data['last_number']
    ];

    if ($type === 'invoice') {

      $Model = new InvoiceSequenceModel();

    } elseif ($type === 'document') {

      $Model = new DocumentSequenceModel();

    } else {

      return $this->finish(
        false,
        'TIPO DE SECUENCIA NO VALIDO'
      );
    }

    $result = $Model->edit(
      $update,
      $id
    );

    if (!$result) {
      return $this->finish(
        false,
        'NO SE PUDO MODIFICAR LA SECUENCIA'
      );
    }

    return $this->finish(
      true,
      'SECUENCIA MODIFICADA CORRECTAMENTE'
    );
  }
  public function delete_open()
  {
    $values = $this->request->getPost();

    $id = $values['id'] ?? null;
    $name = $values['name'] ?? null;
    $type = $values['type'] ?? null;
    $sequenceType = $values['sequence_type'] ?? null;

    if (empty($id)) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (empty($type)) {
      return $this->finish(
        false,
        'NO SE RECIBIO EL TIPO'
      );
    }

    $data = [
      'id' => $id,
      'name' => $name,
      'type' => $type,
      'sequence_type' => $sequenceType
    ];

    log_message(
      'debug',
      'DELETE OPEN DATA: ' . json_encode($data)
    );

    $html = view(
      'Config/components/delete',
      $data
    );

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
  public function delete_save()
  {
    $values = $this->request->getPost();

    $id = $values['id'] ?? null;
    $sequenceType = $values['sequence_type'] ?? null;

    if (empty($id)) {

      return $this->finish(
        false,
        'NO SE RECIBIO EL ID'
      );
    }

    if (empty($sequenceType)) {

      return $this->finish(
        false,
        'NO SE RECIBIO EL TIPO DE SECUENCIA'
      );
    }

    if ($sequenceType === 'invoice') {

      $Model = new InvoiceSequenceModel();

    } elseif ($sequenceType === 'document') {

      $Model = new DocumentSequenceModel();

    } else {

      return $this->finish(
        false,
        'TIPO DE SECUENCIA NO VALIDO'
      );
    }

    if (!$Model->quit($id)) {

      return $this->finish(
        false,
        $Model->get_delete_error() ?: 'ERROR AL ELIMINAR'
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
