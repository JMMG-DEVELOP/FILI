<?php

namespace App\Controllers\Prints;

use App\Controllers\BaseController;
use App\Services\CodeService;
use Throwable;

class Code extends BaseController
{
  /**
   * Preparar e imprimir código de barras.
   */
  public function barcode()
  {
    try {

      $code = trim(
        (string) $this->request->getPost('code')
      );

      $quantity = (int) $this->request->getPost('quantity');

      $deviceId = (int) session('device');

      $service = new CodeService();

      $data = $service->prepareBarcode(
        $code,
        $quantity,
        $deviceId
      );

      return $this->finish(
        true,
        'CÓDIGO DE BARRAS PREPARADO',
        '',
        $data
      );

    } catch (Throwable $e) {

      log_message(
        'error',
        'ERROR PRINT BARCODE: ' . $e->getMessage()
      );

      return $this->finish(
        false,
        $e->getMessage(),
        '',
        []
      );
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
