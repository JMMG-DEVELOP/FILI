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
    $customerModel = new CustomerModel();

    // Obtener POST
    $post = $this->request->getPost();

    // Obtener ID de sesión
    $userId = session()->get('id'); // o 'user_id' según cómo lo guardaste

    if (!$userId) {
      return $this->response->setJSON([
        'status' => false,
        'message' => 'Sesión expirada',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    // Construir data correctamente
    $data = [
      'ci' => trim($post['ci'] ?? ''),
      'name' => trim($post['name'] ?? ''),
      'cel' => trim($post['cel'] ?? ''),
      'correo' => trim($post['correo'] ?? ''),
      'user' => $userId
    ];

    // Guardar
    $result = $customerModel->add($data);

    return $this->response->setJSON([
      'status' => $result['status'],
      'message' => $result['message'],
      'data' => $data,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
}

