<?php
namespace App\Controllers\Customer\Payment;

use App\Controllers\BaseController;
use App\Models\Customer\CustomerPaymentsDatatablesModel;
class Home extends BaseController
{

  public function panel_load()
  {
    $customerId = $this->request->getPost('customer');

    if (empty($customerId) || !is_numeric($customerId)) {
      return $this->finish(
        false,
        'NO SE RECIBIÓ UN CLIENTE VÁLIDO'
      );
    }

    $html = view(
      'customer/modules/payment/home',
      [
        'customer' => (int) $customerId,
      ]
    );

    return $this->finish(
      true,
      'PANEL DE PAGOS CARGADO CORRECTAMENTE',
      $html
    );
  }

  public function payment_datatable()
  {
    $customerId = $this->request->getPost('customer');

    $draw = (int) ($this->request->getPost('draw') ?? 0);

    if (empty($customerId) || !is_numeric($customerId)) {
      return $this->response->setJSON([
        'status' => false,
        'msg' => 'NO SE RECIBIÓ UN CLIENTE VÁLIDO',
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
      ]);
    }

    try {
      $model = new CustomerPaymentsDatatablesModel();

      $result = $model->getDatatable(
        (int) $customerId,
        $this->request->getPost()
      );

      $result['status'] = true;

      return $this->response->setJSON($result);

    } catch (\Throwable $e) {
      log_message(
        'error',
        'ERROR DATATABLE PAGOS CLIENTE: ' . $e->getMessage()
        . ' | Archivo: ' . $e->getFile()
        . ' | Línea: ' . $e->getLine()
      );

      return $this->response->setJSON([
        'status' => false,
        'msg' => 'ERROR AL CONSULTAR LOS PAGOS DEL CLIENTE',
        'debug' => ENVIRONMENT === 'development'
          ? $e->getMessage()
          : null,
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
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