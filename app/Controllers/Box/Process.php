<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\InfoBox;

class Process extends BaseController
{
  public function controller_panel_load()
  {
    $html = view('Box/controller/controller');

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function payment_panel_load()
  {
    // Instanciar modelos
    $infobox = new InfoBox();
    $info = $infobox->payment();
    $html = view('Box/controller/payment', $info);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
  public function customer_panel_load()
  {
    // Instanciar modelos
    $infobox = new InfoBox();
    $info = $infobox->customer();
    $html = view('Box/controller/customer', $info);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
}