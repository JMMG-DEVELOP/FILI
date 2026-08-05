<?php
namespace App\Services;

use App\Models\Box\WaitModel;
use App\Models\Box\WaitDetailsModel;

class WaitService
{
  protected $WaitModel;
  protected $WaitDetailsModel;

  public function __construct()
  {
    $this->WaitModel = new WaitModel();
    $this->WaitDetailsModel = new WaitDetailsModel();
  }

  public function validation($customer)
  {
    return $this->WaitModel->validation($customer);

  }
  public function update($data)
  {

  }
  public function wait($data)
  {
    $response = $this->WaitModel->add_wait($data);

    if (!$response['status']) {
      return $response;
    }

    return [
      'status' => true,
      'wait_id' => $response['id']
    ];
  }

  public function details($data)
  {
    $response = $this->WaitDetailsModel->add_wait_details($data);

    if (!$response) {

      return [
        'status' => false,
        'error' => 'Error al guardar detalle'
      ];
    }

    return [
      'status' => true
    ];
  }


  public function update_wait_mount($wait, $mount)
  {
    $WaitModel = new WaitModel();

    return $WaitModel->update_wait_mount($wait, $mount);
  }

  public function list($wait)
  {
    return $this->WaitDetailsModel->list($wait);

  }

  public function delete($wait)
  {
    $wait_details = $this->WaitDetailsModel->wait_delete($wait);

    if (!$wait_details) {
      return false;
    }

    $wait_head = $this->WaitModel->wait_delete($wait);

    if (!$wait_head) {
      return false;
    }

    return true;
  }
}