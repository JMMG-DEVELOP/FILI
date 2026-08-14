<?php
namespace App\Controllers\Box;
use App\Models\Box\OrdersModel;

use App\Controllers\BaseController;

class Orders extends BaseController
{
  protected $OrdersModel;
  public function __construct()
  {
    $this->OrdersModel = new OrdersModel();
  }
  public function validation()
  {
    $values = $this->request->getPost();
    $code = $values['code'];

    $validation = $this->OrdersModel->getByCode($code);
    if (!$validation) {
      return $this->response->setJSON([
        'status' => false,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
    return $this->response->setJSON([
      'status' => true,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function order_add()
  {
    $values = $this->request->getPost();
    $order = [
      'product' => $values['product'],
      'user' => $values['user'],
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
      'status' => 1
    ];
    $operation = $this->OrdersModel->order_add($order);
    if (!$operation) {
      return $this->response->setJSON([
        'status' => false,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
    return $this->response->setJSON([
      'status' => true,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
}
