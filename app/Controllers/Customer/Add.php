<?php
namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\Customer\CustomerModel;

class Add extends BaseController
{

  public function open()
  {
    $html = view('Customer/components/form');
    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }


  public function save()
  {
    $post = $this->request->getPost();
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
      'ci' => trim($post['ci'] ?? ''),
      'name' => trim($post['name'] ?? ''),
      'cel' => trim($post['cel'] ?? ''),
      'correo' => trim($post['correo'] ?? ''),
      'user' => $userId
    ];

    $customerModel = new CustomerModel();
    $result = $customerModel->add($data);

    return $this->response->setJSON([
      'status' => $result['status'],
      'message' => $result['message'],
      'data' => $result['data'] ?? null,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
}

