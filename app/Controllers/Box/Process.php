<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Auth\UsersSucursals;
use App\Models\Box\UserBoxModel;
use App\Models\Box\PaymentTypeModel;
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
  public function expedition_point_load()
  {
    // Instanciar modelos
    $userSucursal = new UsersSucursals();
    // $userBox = new UserBoxModel();

    $data = [
      'sucursal' => $userSucursal->details(session()->get('id')),
      'user' => session()->get('id'),
      'session' => session()->get('session'),
      'box' => session()->get('box')
    ];

    $html = view('Box/controller/expedition_point', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function expedition_point_select()
  {
    $userBox = new UserBoxModel();
    $user = $this->request->getPost('user');
    $sucursal = $this->request->getPost('sucursal');

    $values = $userBox->getUserExpedition($user, $sucursal);

    return $this->response->setJSON([
      'status' => true,
      'values' => $values,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function sales_cash_credit_confirm()
  {
    $PaymentTypeModel = new PaymentTypeModel();
    $data = [
      'payments' => $PaymentTypeModel
        ->where('id >=', 2)
        ->findAll(),
    ];
    // Instanciar modelos
    $html = view('Box/components/procedure_confirm', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }


  public function box_movement_panel_load()
  {
    // Instanciar modelos
    $infobox = new InfoBox();
    $info = $infobox->box_movements();
    $html = view('Box/controller/box_movement', $info);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }
}