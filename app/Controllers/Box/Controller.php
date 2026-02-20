<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;

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


}
