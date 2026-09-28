<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;
use App\Models\Box\CustomerModel;
use App\Models\Box\BoxMovementModel;
use App\Models\Box\BoxModel;



class Controller extends BaseController
{
  public function open_box()
  {
    /*
     * =====================================================
     * 1. VERIFICAR LOGIN
     * =====================================================
     */
    if (!session()->get('logged')) {

      return $this->response->setJSON([
        'status' => false,
        'message' => 'La sesión no es válida.'
      ]);
    }


    /*
     * =====================================================
     * 2. DATOS DE SESIÓN
     * =====================================================
     */
    $userId = session()->get('id');
    $sessionId = session()->get('session');


    if (!$userId || !$sessionId) {

      return $this->response->setJSON([
        'status' => false,
        'message' => 'La sesión de usuario no es válida.'
      ]);
    }


    /*
     * =====================================================
     * 3. OBTENER MONTO DE APERTURA
     * =====================================================
     */
    $amount = (float) $this->request->getPost('cash_total');


    if ($amount < 0) {

      return $this->response->setJSON([
        'status' => false,
        'message' => 'El monto de apertura no puede ser negativo.'
      ]);
    }


    /*
     * =====================================================
     * 4. MODELOS
     * =====================================================
     */
    $boxModel = new BoxModel();
    $boxMovementModel = new BoxMovementModel();


    /*
     * =====================================================
     * 5. VERIFICAR QUE NO TENGA OTRA CAJA ABIERTA
     * =====================================================
     */
    $openBox = $boxModel->get_open_box($userId);

    if ($openBox) {

      /*
       * Si ya existe una caja abierta,
       * simplemente guardar su ID en session.
       */
      session()->set([
        'box' => $openBox['id']
      ]);

      return $this->response->setJSON([
        'status' => true,
        'message' => 'Ya existe una caja abierta.',
        'redirect' => base_url('box')
      ]);
    }


    /*
     * =====================================================
     * 6. INICIAR TRANSACCIÓN
     * =====================================================
     */
    $db = \Config\Database::connect();

    $db->transStart();


    /*
     * =====================================================
     * 7. CREAR CAJA
     * =====================================================
     */
    $boxId = $boxModel->add_box([
      'user' => $userId,
      'session' => $sessionId,
      'status' => 1
    ]);


    if (!$boxId) {

      $db->transRollback();

      return $this->response->setJSON([
        'status' => false,
        'message' => 'No se pudo crear la caja.'
      ]);
    }


    /*
     * =====================================================
     * 8. REGISTRAR MOVIMIENTO DE APERTURA
     * =====================================================
     */
    $movement = $boxMovementModel->insert([
      'mount' => $amount,
      'box' => $boxId,
      'sales' => null,
      'type' => 4,
      'sales_type' => 1,
      'sales_payment' => 1
    ]);


    if (!$movement) {

      $db->transRollback();

      return $this->response->setJSON([
        'status' => false,
        'message' => 'No se pudo registrar la apertura de caja.'
      ]);
    }


    /*
     * =====================================================
     * 9. FINALIZAR TRANSACCIÓN
     * =====================================================
     */
    $db->transComplete();


    if (!$db->transStatus()) {

      return $this->response->setJSON([
        'status' => false,
        'message' => 'No se pudo completar la apertura de caja.'
      ]);
    }


    /*
     * =====================================================
     * 10. GUARDAR CAJA EN SESSION
     * =====================================================
     */
    session()->set([
      'box' => $boxId
    ]);


    /*
     * =====================================================
     * 11. RESPUESTA
     * =====================================================
     */
    return $this->response->setJSON([
      'status' => true,
      'message' => 'Caja abierta correctamente.',
      'box' => $boxId,
      'redirect' => base_url('box')
    ]);
  }
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
    $values = $this->request->getPost();

    $data = [
      'type' => $values['type'],
      'mount' => $values['mount'],
      'box' => session()->get('box'),
      'sales' => 1,
      'sales_type' => 1,
      'sales_payment' => 1
    ];

    $operation = $BoxMovementModel->add_box_movement($data);
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
