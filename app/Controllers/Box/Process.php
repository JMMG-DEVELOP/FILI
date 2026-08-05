<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Auth\UsersSucursals;
use App\Models\Box\UserBoxModel;
use App\Models\Box\PaymentTypeModel;
use App\Models\Box\InvoiceTypeModel;
use App\Models\Box\WaitModel;


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
  public function print_panel_load()
  {
    $InvoiceType = new InvoiceTypeModel();

    $data = [
      'types' => $InvoiceType->findAll()
    ];

    $html = view('Box/controller/print', $data);

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

  public function history_sales_panel_load()
  {
    $BoxMovementModel = new \App\Models\Box\BoxMovementModel();

    $page = (int) ($this->request->getVar('page') ?? 1);

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $filters = [
      'box' => session('box'),
      'type' => 1
    ];

    $total = $BoxMovementModel->countMovements($filters);

    $sales = $BoxMovementModel->getMovements(
      $filters,
      $limit,
      $offset
    );

    $totalPages = ceil($total / $limit);

    $html = view('box/components/history_sales', [
      'sales' => $sales,
      'page' => $page,
      'totalPages' => $totalPages
    ]);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
  public function history_movements_panel_load()
  {
    try {

      $BoxMovementModel = new \App\Models\Box\BoxMovementModel();

      $movements = $BoxMovementModel->getMovements([
        'box' => session('box'),
        'type_not' => 1
      ]);

      $html = view(
        'box/components/history_movements',
        [
          'movements' => $movements
        ]
      );

      return $this->response->setJSON([
        'status' => true,
        'html' => $html,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);

    } catch (\Throwable $th) {

      return $this->response->setJSON([
        'status' => false,
        'message' => $th->getMessage()
      ]);

    }

  }
  public function wait_panel_load()
  {
    $WaitModel = new WaitModel();

    $data = [
      'data' => $WaitModel->getList()
    ];

    $html = view('Box/components/wait', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }
}