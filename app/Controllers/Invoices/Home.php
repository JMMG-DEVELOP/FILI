<?php
namespace App\Controllers\Invoices;

use App\Controllers\BaseController;
use App\Models\Invoices\InvoicesDatatableModel;
class Home extends BaseController
{
  protected $Model;
  public function __construct()
  {
    $this->Model = new InvoicesDatatableModel();
  }

  public function card_load()
  {
    try {
      $model = new \App\Models\Invoices\InvoicesModel();

      $data = $model->get_card();

      return $this->response->setJSON([
        'status' => true,
        'data' => $data
      ]);
    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR AL CARGAR CARDS DE FACTURAS: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->response->setJSON([
        'status' => false,
        'msg' => 'NO SE PUDIERON CARGAR LOS INDICADORES'
      ]);
    }
  }
  public function panel_load()
  {

    $html = view(
      'invoices/home/home',
    );

    return $this->finish(
      true,
      'VENTAS CARGADAS CORRECTAMENTE',
      $html
    );
  }

  public function home_datatable()
  {
    $draw = (int) ($this->request->getPost('draw') ?? 0);

    try {
      $model = new InvoicesDatatableModel();

      $result = $model->getDatatable(
        $this->request->getPost()
      );

      return $this->response->setJSON([
        'status' => true,
        'draw' => $result['draw'],
        'recordsTotal' => $result['recordsTotal'],
        'recordsFiltered' => $result['recordsFiltered'],
        'data' => $result['data']
      ]);
    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR DATATABLE FACTURAS: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->response->setJSON([
        'status' => false,
        'msg' => 'ERROR AL CONSULTAR LAS FACTURAS',
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => []
      ]);
    }
  }

  public function cancel_invoice()
  {
    $id = $this->request->getPost('id');

    if (empty($id) || !is_numeric($id) || (int) $id <= 0) {
      return $this->response->setJSON([
        'status' => false,
        'msg' => 'NO SE RECIBIÓ UNA FACTURA VÁLIDA'
      ]);
    }

    $model = new InvoicesDatatableModel();

    $invoice = $model->find((int) $id);

    if (empty($invoice)) {
      return $this->response->setJSON([
        'status' => false,
        'msg' => 'LA FACTURA NO EXISTE'
      ]);
    }

    if ((int) $invoice['status'] === 2) {
      return $this->response->setJSON([
        'status' => false,
        'msg' => 'LA FACTURA YA ESTÁ ANULADA'
      ]);
    }

    $db = \Config\Database::connect();

    try {
      if (!$db->table('status')->where('id', 2)->countAllResults()) {
        return $this->response->setJSON([
          'status' => false,
          'msg' => 'NO EXISTE EL ESTADO DE ANULACIÓN'
        ]);
      }

      if (!$model->update((int) $id, ['status' => 2])) {
        return $this->response->setJSON([
          'status' => false,
          'msg' => 'NO SE PUDO ANULAR LA FACTURA',
          'errors' => $model->errors()
        ]);
      }

      return $this->response->setJSON([
        'status' => true,
        'msg' => 'FACTURA ANULADA CORRECTAMENTE'
      ]);
    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR AL ANULAR FACTURA: ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return $this->response->setJSON([
        'status' => false,
        'msg' => 'ERROR AL ANULAR LA FACTURA'
      ]);
    }
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
