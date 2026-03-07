<?php
namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\Customer\CustomerModel;

class Process extends BaseController
{
  public function customer_panel_load()
  {
    $customerModel = new CustomerModel();

    $ci = $this->request->getPost('ci') ?: '1';

    $result = $customerModel->search_ci($ci);

    if (!$result) {
      $result = [
        'ci' => '',
        'name' => ''
      ];
    }

    $html = view('Box/controller/customer', [
      'result' => $result
    ]);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
  public function search()
  {
    if ($this->request->isAJAX()) {
      $customerModel = new CustomerModel();
      $value = $this->request->getPost('value');
      $result = [
        'result' => $customerModel->search($value)
      ];
      $html = view('Customer/components/search_table', $result);


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