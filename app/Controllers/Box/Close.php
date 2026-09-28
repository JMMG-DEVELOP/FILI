<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\Infopage;
use App\Models\Box\BoxMovementModel;
use App\Models\Auth\UsersSessionsModel;
use App\Models\Box\BoxModel;

class Close extends BaseController
{
  protected $BoxMovementModel;
  public function __construct()
  {
    $this->BoxMovementModel = new BoxMovementModel();
  }
  public function index()
  {

    if (session()->get('logged')) {

      $infopage = new Infopage();

      $info = [
        'title' => 'Cierre de Caja',
        'type' => 'close'
      ];
      $page = $infopage->infopage($info);
      return view('box/components/open_close', $page);

    }
  }

  public function box_close_resume()
  {
    $values = $this->request->getPost();
    $UsersSessionsModel = new UsersSessionsModel();
    $session = session();


    $BoxModel = new BoxModel();
    if (!$BoxModel->close_box($values['box'])) {
      return [
        'status' => false,
        'error' => 'ERROR AL CERRAR LA CAJA',
        'model_errors' => $BoxModel->errors(),
        'db_error' => $BoxModel->db->error(),
      ];
    }
    if (!$UsersSessionsModel->close_session(session()->get('session'))) {
      return [
        'status' => false,
        'error' => 'ERROR AL CERRAR LA SESIÓN',
        'model_errors' => $UsersSessionsModel->errors(),
        'db_error' => $UsersSessionsModel->db->error(),
      ];
    }
    $session->destroy();
    $data = [
      'values' => $values
    ];
    $html = view('Box/components/close_resume', $data);

    return $this->response->setJSON([
      'status' => true,
      'html' => $html,
      'values' => $data,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);

  }
  public function box_close_save()
  {
    $values = $this->request->getPost();

    $box = session()->get('box');

    if (empty($box)) {

      return $this->response->setJSON([
        'status' => false,
        'html' => 'No existe una caja activa.',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }


    /*
     * ==========================================
     * VALORES DEL CIERRE
     * ==========================================
     */

    $cash_total =
      (float) ($values['cash_total'] ?? 0);

    $qr_total =
      (float) ($values['qr_total'] ?? 0);

    $transferencia_total =
      (float) ($values['transferencia_total'] ?? 0);

    $tarjeta_total =
      (float) ($values['tarjeta_total'] ?? 0);


    /*
     * ==========================================
     * OBTENER MOVIMIENTOS DE LA CAJA
     * ==========================================
     */

    $BoxMovementModel =
      new \App\Models\Box\BoxMovementModel();

    $movements =
      $BoxMovementModel->box_movement_totals();


    /*
     * ==========================================
     * TOTAL SISTEMA - EFECTIVO
     * ==========================================
     */

    $system_cash =
      $movements['movement_opening']
      + $movements['movement_cash']
      - $movements['movement_devolution_cash']
      - $movements['movement_null_cash']
      - $movements['movement_retiro'];


    /*
     * ==========================================
     * TOTAL SISTEMA - QR
     * ==========================================
     */

    $system_qr =
      $movements['movement_qr']
      - $movements['movement_devolution_qr']
      - $movements['movement_null_qr'];


    /*
     * ==========================================
     * TOTAL SISTEMA - TRANSFERENCIA
     * ==========================================
     */

    $system_transfer =
      $movements['movement_transfer']
      - $movements['movement_devolution_transfer']
      - $movements['movement_null_transfer'];


    /*
     * ==========================================
     * TOTAL SISTEMA - TARJETA
     * ==========================================
     */

    $system_card =
      $movements['movement_card']
      - $movements['movement_devolution_card']
      - $movements['movement_null_card'];


    /*
     * ==========================================
     * DIFERENCIAS
     * ==========================================
     */

    $difference_cash =
      $cash_total - $system_cash;

    $difference_qr =
      $qr_total - $system_qr;

    $difference_transfer =
      $transferencia_total - $system_transfer;

    $difference_card =
      $tarjeta_total - $system_card;

    $system_credit =
      $movements['movement_credit']
      - $movements['movement_devolution_credit']
      - $movements['movement_null_credit'];

    /*
     * ==========================================
     * GUARDAR CIERRE
     * ==========================================
     */

    $BoxClosingModel =
      new \App\Models\Box\BoxClosingModel();

    $db = \Config\Database::connect();

    $db->transStart();


    /*
     * EFECTIVO
     */
    $BoxClosingModel->insert([
      'box' => $box,
      'payment' => 1,
      'system_mount' => $system_cash,
      'close_mount' => $cash_total,
      'diference_mount' => $difference_cash
    ]);


    /*
     * QR
     */
    $BoxClosingModel->insert([
      'box' => $box,
      'payment' => 2,
      'system_mount' => $system_qr,
      'close_mount' => $qr_total,
      'diference_mount' => $difference_qr
    ]);


    /*
     * TRANSFERENCIA
     */
    $BoxClosingModel->insert([
      'box' => $box,
      'payment' => 3,
      'system_mount' => $system_transfer,
      'close_mount' => $transferencia_total,
      'diference_mount' => $difference_transfer
    ]);


    /*
     * TARJETA
     */
    $BoxClosingModel->insert([
      'box' => $box,
      'payment' => 4,
      'system_mount' => $system_card,
      'close_mount' => $tarjeta_total,
      'diference_mount' => $difference_card
    ]);

    $BoxClosingModel->insert([
      'box' => $box,
      'payment' => 5,
      'system_mount' => $system_credit,
      'close_mount' => $system_credit,
      'diference_mount' => 0
    ]);


    $db->transComplete();


    if ($db->transStatus() === false) {

      return $this->response->setJSON([
        'status' => false,
        'msg' => 'No se pudo registrar el cierre de caja.',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }


    return $this->response->setJSON([

      'status' => true,

      'msg' => 'Cierre registrado correctamente.',

      'box' => $box,

      'closing' => [

        'cash' => [
          'system' => $system_cash,
          'close' => $cash_total,
          'difference' => $difference_cash
        ],

        'qr' => [
          'system' => $system_qr,
          'close' => $qr_total,
          'difference' => $difference_qr
        ],

        'transfer' => [
          'system' => $system_transfer,
          'close' => $transferencia_total,
          'difference' => $difference_transfer
        ],

        'card' => [
          'system' => $system_card,
          'close' => $tarjeta_total,
          'difference' => $difference_card
        ],
        'credit' => [
          'system' => $system_credit,
          'close' => $system_credit,
          'difference' => 0
        ]

      ],

      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()

    ]);
  }

}
