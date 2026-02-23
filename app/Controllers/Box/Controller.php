<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;
use App\Models\Box\CustomerModel;

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

  public function customer_search()
  {
    if ($this->request->isAJAX()) {
      $customerModel = new CustomerModel();
      $value = $this->request->getPost('value');
      $result = [
        'result' => $customerModel->search($value)
      ];
      $html = view('Box/components/customer_search_table', $result);


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
