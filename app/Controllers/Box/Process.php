<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Models\Auth\UsersSucursals;
use App\Models\Box\UserBoxModel;
use App\Models\Box\PaymentTypeModel;
use App\Models\Box\InvoiceTypeModel;
use App\Models\Box\WaitModel;
use App\Models\Box\PaymentDeviceModel;
use App\Models\Box\SalesDetailsModel;

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

  public function invoice_cash_panel_load()
  {
    $html = view('Box/invoice/cash_payment');

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }

  public function invoice_digits_panel_load()
  {
    $PaymentDeviceModel = new PaymentDeviceModel();

    $data = [
      'devices' => $PaymentDeviceModel->findAll()
    ];

    $html = view('Box/invoice/digits_payment', $data);

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

  public function invoice_multi_payment_load()
  {
    $PaymentDeviceModel = new PaymentDeviceModel();
    $PaymentTypeModel = new PaymentTypeModel();
    $data = [
      'payments' => $PaymentTypeModel
        ->where('id >=', 2)
        ->findAll(),
      'devices' => $PaymentDeviceModel->findAll()

    ];
    // Instanciar modelos
    $html = view('Box/invoice/multi_payment', $data);

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

    $limit = 10;

    $pageCash = max(
      1,
      (int) ($this->request->getVar('page_cash') ?? 1)
    );

    $pageCredit = max(
      1,
      (int) ($this->request->getVar('page_credit') ?? 1)
    );

    $pageOther = max(
      1,
      (int) ($this->request->getVar('page_other') ?? 1)
    );

    // EFECTIVO
    $filtersCash = [
      'box' => session('box'),
      'type' => 1,
      'sales_type' => 1,
      'payment' => 1
    ];

    $totalCash = $BoxMovementModel->countMovements($filtersCash);

    $offsetCash = ($pageCash - 1) * $limit;

    $cash = $BoxMovementModel->getMovements(
      $filtersCash,
      $limit,
      $offsetCash
    );

    $totalPagesCash = max(
      1,
      (int) ceil($totalCash / $limit)
    );

    // CRÉDITO
    $filtersCredit = [
      'box' => session('box'),
      'sales_type' => 2
    ];

    $totalCredit = $BoxMovementModel->countMovements($filtersCredit);

    $offsetCredit = ($pageCredit - 1) * $limit;

    $credit = $BoxMovementModel->getMovements(
      $filtersCredit,
      $limit,
      $offsetCredit
    );

    $totalPagesCredit = max(
      1,
      (int) ceil($totalCredit / $limit)
    );

    // OTROS
    $filtersOther = [
      'box' => session('box'),
      'sales_type' => 1,
      'payment_not' => 1
    ];

    $totalOther = $BoxMovementModel->countMovements($filtersOther);

    $offsetOther = ($pageOther - 1) * $limit;

    $other = $BoxMovementModel->getMovements(
      $filtersOther,
      $limit,
      $offsetOther
    );

    $totalPagesOther = max(
      1,
      (int) ceil($totalOther / $limit)
    );

    $html = view('box/components/history_sales', [
      'cash' => $cash,
      'credit' => $credit,
      'other' => $other,

      'pageCash' => $pageCash,
      'pageCredit' => $pageCredit,
      'pageOther' => $pageOther,

      'totalPagesCash' => $totalPagesCash,
      'totalPagesCredit' => $totalPagesCredit,
      'totalPagesOther' => $totalPagesOther
    ]);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  public function history_sales_details_panel_load()
  {
    try {

      $SalesDetailsModel = new SalesDetailsModel();

      $values = $SalesDetailsModel->get_id($this->request->getPost('id'));

      $html = view(
        'box/components/history_sales_detail',
        [
          'sales' => $values
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

  public function history_movements_panel_load()
  {
    try {

      $BoxMovementModel = new \App\Models\Box\BoxMovementModel();

      $movements = $BoxMovementModel->getMovements([
        'box' => session('box'),

        'type_not_in' => [1, 5]
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

  public function invoice_product_panel_load()
  {
    // Instanciar modelos

    $html = view('Box/invoice/product_panel');

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }
}