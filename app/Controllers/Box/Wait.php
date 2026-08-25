<?php

namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\InfoWait;
use App\Services\WaitService;

class Wait extends BaseController
{
  protected $InfoWait;
  protected $WaitService;

  public function __construct()
  {
    $this->InfoWait = new InfoWait();
    $this->WaitService = new WaitService();
  }


  public function validation()
  {
    $values = $this->request->getPost();
    // $values = $this->InfoWait->formatter($values);

    $validation = $this->WaitService->validation($values['customer']['customer_id']);
    return $this->response->setJSON([
      'status' => $validation,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function list()
  {
    $values = $this->request->getPost();
    $list = $this->WaitService->list($values['wait']);

    return $this->response->setJSON([
      'status' => true,
      'values' => $list,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function update()
  {
    $values = $this->request->getPost();

    $db = \Config\Database::connect();
    $db->transStart();

    // Guardar los nuevos detalles
    $response = $this->WaitService->details(
      $this->InfoWait->wait_details($values, $values['wait'])
    );

    if (!$response) {
      $db->transRollback();

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al guardar los detalles.',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    // Actualizar monto total
    $mount = $values['mount'] + $values['cart']['totals']['total_price'];

    $response = $this->WaitService->update_wait_mount(
      $values['wait'],
      $mount
    );

    if (!$response) {
      $db->transRollback();

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al actualizar el monto.',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    $db->transComplete();

    if (!$db->transStatus()) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error en la transacción.',
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

  public function wait_save()
  {
    $values = $this->request->getPost();

    // Formatear datos recibidos
    $values = $this->InfoWait->formatter($values);

    $db = \Config\Database::connect();
    $db->transStart();

    // CABECERA
    $wait = $this->InfoWait->wait($values);

    $wait_operation = $this->WaitService->wait($wait);

    if (!$wait_operation['status']) {
      return $this->json($wait_operation, 400);
    }

    $waitid = $wait_operation['wait_id'];

    // DETALLE
    $response = $this->execute(
      $this->WaitService->details(
        $this->InfoWait->wait_details($values, $waitid)
      )
    );

    if ($response)
      return $response;

    $db->transComplete();

    if ($db->transStatus() === false) {

      return $this->response->setJSON([
        'status' => false,
        'wait_id' => $values,
        'error' => 'Error en la transacción',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
    return $this->response->setJSON([
      'status' => true,
      'wait_id' => $values,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function delete()
  {
    $values = $this->request->getPost();
    $db = \Config\Database::connect();
    $db->transStart();

    $response = $this->execute(
      $this->WaitService->delete($values['wait'])
    );

    if ($response)
      return $response;

    $db->transComplete();

    if ($db->transStatus() === false) {

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error en la transacción',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
    return $this->response->setJSON([
      'status' => true,
      'wait_id' => $values,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);


  }
  public function wait_delete()
  {
    $values = $this->request->getPost();

    $db = \Config\Database::connect();
    $db->transStart();

    $response = $this->WaitService->delete($values['wait']);

    if (!$response) {
      $db->transRollback();

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al eliminar la espera.',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    $db->transComplete();

    if (!$db->transStatus()) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error en la transacción.',
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
  private function execute($operation)
  {
    if (!$operation['status']) {
      return $this->json($operation, 400);
    }

    return null;
  }
}