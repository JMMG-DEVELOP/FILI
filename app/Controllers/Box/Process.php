<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;

class Process extends BaseController
{
  public function search()
  {
    if (!$this->request->isAJAX()) {
      return $this->response
        ->setStatusCode(403)
        ->setJSON([
          'status' => false,
          'message' => 'ERROR 403',
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash()
        ]);
    }
    if ($this->request->isAJAX()) {
      $productModel = new ProductModel();
      $value = $this->request->getPost('value');
      $result = [
        'result' => $productModel->getBySearch($value)
      ];
      $html = view('Box/Product/search_table', $result);


      return $this->response->setJSON([
        'status' => true,
        'message' => $result,
        'html' => $html,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);

    }

  }


}
