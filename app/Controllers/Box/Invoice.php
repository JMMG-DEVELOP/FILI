<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;

class Invoice extends BaseController
{
  public function product_add()
  {
    if (!$this->request->isAJAX()) {
      return $this->response->setStatusCode(403);
    }
    $code = $this->request->getPost('code');

    if (!$code) {
      return $this->response->setJSON([
        'status' => false,
        'message' => 'Código no enviado',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
    $productModel = new ProductModel();
    // Buscar producto
    $product = $productModel->getByCode($code);

    if (!$product) {
      return $this->response->setJSON([
        'status' => false,
        'message' => 'PRODUCTO NO ENCONTRADO',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()

      ]);
    }

    // Retornar producto
    return $this->response->setJSON([
      'status' => true,
      'data' => $product,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);


  }
}