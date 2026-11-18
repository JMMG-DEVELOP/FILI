<?php

namespace App\Controllers\Prints;

use App\Controllers\BaseController;
use App\Services\NameService;
use Throwable;

class Name extends BaseController
{
  public function print()
  {
    try {

      $name = (string) $this->request->getPost('name');

      $quantity = (int) $this->request->getPost('quantity');

      $deviceId = (int) session('device');

      $service = new NameService();

      $data = $service->prepareName(
        $name,
        $quantity,
        $deviceId
      );

      return $this->finish(
        true,
        'NOMBRE PREPARADO PARA IMPRESIÓN',
        '',
        $data
      );

    } catch (Throwable $e) {

      log_message(
        'error',
        'ERROR AL PREPARAR IMPRESIÓN DE NOMBRE: '
        . $e->getMessage()
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
