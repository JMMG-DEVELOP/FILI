<?php

namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\InfoSales;
use App\Models\Box\DocumentSequenceModel;
use App\Models\Box\InvoiceSequenceModel;
use App\Services\SalesService;

class Sales extends BaseController
{
  protected $InfoSales;
  protected $InvoiceSequenceModel;
  protected $DocumentSequenceModel;
  protected $SalesService;

  public function __construct()
  {
    $this->InfoSales = new InfoSales();
    $this->InvoiceSequenceModel = new InvoiceSequenceModel();
    $this->DocumentSequenceModel = new DocumentSequenceModel();
    $this->SalesService = new SalesService();
  }

  public function sales_cash_payment()
  {
    return $this->processSale();
  }

  public function sales_cash_credit_payment()
  {
    return $this->processSale(true);
  }

  private function processSale($withCredit = false)
  {
    $values = $this->request->getPost();
    $values = $this->InfoSales->formatter($values);

    $db = \Config\Database::connect();
    $db->transStart();

    // CABECERA
    $sales = $this->InfoSales->sales($values);

    $sale_operation = $this->SalesService->sales($sales);

    if (!$sale_operation['status']) {
      return $this->json($sale_operation, 400);
    }

    $sale_id = $sale_operation['sale_id'];

    // DETALLE
    $response = $this->execute(
      $this->SalesService->details(
        $this->InfoSales->sales_details($values, $sale_id)
      )
    );

    if ($response)
      return $response;

    // PAGO
    $response = $this->execute(
      $this->SalesService->payment_cash(
        $this->InfoSales->sales_payment_cash($values, $sale_id)
      )
    );

    if ($response)
      return $response;

    // STOCK
    $response = $this->execute(
      $this->SalesService->discountStock(
        $this->InfoSales->stock_update($values)
      )
    );

    if ($response)
      return $response;

    // HISTORIAL
    $response = $this->execute(
      $this->SalesService->historyStock(
        $this->InfoSales->stock_movements($values, $sale_id)
      )
    );

    if ($response)
      return $response;

    // ACTUALIZAR SECUENCIA
    $sequenceId = (int) $values['point']['sequence_id'];

    if ((int) $values['receipt'] === 1) {
      $this->DocumentSequenceModel->update_last_number($sequenceId);
    } else {
      $this->InvoiceSequenceModel->update_last_number($sequenceId);
    }

    // MOVIMIENTO DE CAJA
    $response = $this->execute(
      $this->SalesService->boxMovements(
        $this->InfoSales->box_movements($values, $sale_id)
      )
    );

    if ($response)
      return $response;

    // CREDITO
    if ($withCredit) {

      // lógica crédito
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
      'sale_id' => $sale_id,
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