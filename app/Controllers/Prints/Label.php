<?php

namespace App\Controllers\Prints;

use App\Controllers\BaseController;
use App\Services\LabelService;
use Throwable;

class Label extends BaseController
{
  public function print()
  {
    try {

      $code = trim(
        (string) $this->request->getPost('code')
      );

      $description = trim(
        (string) $this->request->getPost('description')
      );

      $priceOne = trim(
        (string) $this->request->getPost('price_1')
      );

      $priceTwo = trim(
        (string) $this->request->getPost('price_2')
      );

      $cantTwo = trim(
        (string) $this->request->getPost('cant_2')
      );

      $quantity = (int) $this->request->getPost('quantity');

      $deviceId = (int) session('device');

      $service = new LabelService();

      $result = $service->prepareLabel(
        $code,
        $description,
        $priceOne,
        $priceTwo,
        $cantTwo,
        $quantity,
        $deviceId
      );

      return $this->response->setJSON([
        'status' => true,
        'msg' => 'ETIQUETA PREPARADA CORRECTAMENTE.',
        'data' => $result,
      ]);

    } catch (Throwable $e) {

      log_message(
        'error',
        'ERROR IMPRESIÓN LABEL: ' . $e->getMessage()
      );

      return $this->response->setJSON([
        'status' => false,
        'msg' => $e->getMessage(),
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
