<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;
use App\Models\Box\CustomerModel;
use App\Models\Box\BoxMovementModel;


class Controller extends BaseController
{
  public function product_search()
  {

    if ($this->request->isAJAX()) {
      $productModel = new ProductModel();
      $value = $this->request->getPost('value');
      $result = [
        'result' => $productModel->getBySearch($value)
      ];
      $html = view('Box/components/product_search_table', $result);

      return $this->response->setJSON([
        'status' => true,
        'message' => $result,
        'html' => $html,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);

    }

  }

  public function product_form()
  {
    if ($this->request->isAJAX()) {

      $productModel = new ProductModel();
      $value = $this->request->getPost('value');

      $product = $productModel->getByCode($value);

      if (!$product) {
        return $this->response->setJSON([
          'status' => false,
          'message' => 'Producto no encontrado'
        ]);
      }

      $html = view('Box/controller/product', [
        'result' => $product
      ]);

      return $this->response->setJSON([
        'status' => true,
        'product' => $product,
        'html' => $html,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
  }


  public function customer_add()
  {

    $customerModel = new CustomerModel();

    $userId = session()->get('id');

    if (!$userId) {
      return $this->response->setJSON([
        'status' => false,
        'message' => 'Sesión expirada',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    $data = [
      'ci' => trim($this->request->getPost('ruc_ci')),
      'name' => trim($this->request->getPost('customer_name')),
      'cel' => trim($this->request->getPost('customer_cel')),
      'correo' => trim($this->request->getPost('customer_correo')),
      'user' => $userId // 🔥 ID NUMÉRICO REAL
    ];

    $result = $customerModel->createCustomer($data);

    return $this->response->setJSON([
      'status' => $result['status'],
      'message' => $result['message'],
      'data' => $result['data'] ?? null,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function box_movement_send()
  {

    $BoxMovementModel = new BoxMovementModel();
    $operation = $BoxMovementModel->add_box_movement($this->request->getPost());
    if ($operation === false) {

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error en la transacción',
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
