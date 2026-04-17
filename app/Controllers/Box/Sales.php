<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\InfoSales;
use App\Models\Box\SalesModel;
use App\Models\Box\SalesDetailsModel;
use App\Models\Box\BoxMovementModel;
use App\Models\Box\DocumentSequenceModel;
use App\Models\Box\InvoiceSequenceModel;
use App\Models\Box\StockMovmentsModel;
use App\Models\Products\Products\StockModel;



class Sales extends BaseController
{

  public function sales_cash_payment()
  {
    $InfoSales = new InfoSales();
    $SalesModel = new SalesModel();
    $SalesDetailsModel = new SalesDetailsModel();
    $InvoiceSequenceModel = new InvoiceSequenceModel();
    $DocumentSequenceModel = new DocumentSequenceModel();
    $BoxMovementModel = new BoxMovementModel();
    $StockMovmentsModel = new StockMovmentsModel();
    $StockModel = new StockModel();


    $values = $this->request->getPost();
    $values = $InfoSales->formatter($values);

    $db = \Config\Database::connect();
    $db->transStart();

    // 🔹 CABECERA
    $sales = $InfoSales->sales($values);
    $sale_id = $SalesModel->add_sales($sales);

    if (!$sale_id) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al guardar cabecera'
      ]);
    }

    // 🔹 DETALLE
    $details = $InfoSales->sales_details($values, $sale_id);
    $ok = $SalesDetailsModel->add_sales_details($details);

    if (!$ok) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al guardar detalle',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    // ACTUALIZACIÓN DE STOCK
    $stock = $InfoSales->stock_update($values);
    $ok = $StockModel->discountStock($stock);

    if (!$ok) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al actualizar stock',
        'data' => $stock,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }


    // HISTORIAL
    $history = $InfoSales->stock_movements($values, $sale_id);
    $ok = $StockMovmentsModel->add_stock_movement($history);

    if (!$ok) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al guardar movimiento de stock',
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    // ACTUALIZAR NUMERO DE POINT

    $sequenceId = (int) $values['point']['sequence_id'];

    if ((int) $values['receipt'] === 1) {
      $newNumber = $DocumentSequenceModel->update_last_number($sequenceId);

    } else {

      $newNumber = $InvoiceSequenceModel->update_last_number($sequenceId);

    }

    // GUARDAR EN BOX_MOVEMENT PARA CONTROL DE CAJA
    //TYPE 1
    $movement = $InfoSales->box_movements($values, $sale_id);

    $ok = $BoxMovementModel->add_box_movement($movement);

    if (!$ok) {
      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error al guardar Movimiento de Caja',
        'data' => $movement,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }
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
      'normalize' => $values,
      'sale_id' => $sale_id,
      'stock' => $stock,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

}