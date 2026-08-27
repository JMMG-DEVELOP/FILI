<?php

namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\InfoSales;
use App\Models\Box\DocumentSequenceModel;
use App\Models\Box\InvoiceSequenceModel;
use App\Services\SalesService;
use App\Services\CustomerService;

class Sales extends BaseController
{
  protected $InfoSales;
  protected $InvoiceSequenceModel;
  protected $DocumentSequenceModel;
  protected $SalesService;
  protected $CustomerService;


  public function __construct()
  {
    $this->InfoSales = new InfoSales();
    $this->InvoiceSequenceModel = new InvoiceSequenceModel();
    $this->DocumentSequenceModel = new DocumentSequenceModel();
    $this->SalesService = new SalesService();
    $this->CustomerService = new CustomerService();

  }

  public function sales_cash_payment()
  {
    return $this->processSale(true);
  }
  public function sales_credit_payment()
  {
    return $this->processSale(false, true);
  }

  public function sales_cash_credit_payment()
  {
    return $this->processSale(true);
  }

  public function sales_devolution()
  {
    return $this->processSale(false, false, false, false, true);
  }
  private function processSale($withCash = false, $withCredit = false, $withCashCredit = false, $withCashDigist = false, $withDevolution = false)
  {
    $values = $this->request->getPost();

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
      $this->SalesService->sales_details(
        $this->InfoSales->sales_details($values, $sale_id)
      )
    );

    if ($response)
      return $response;

    // SALES IVA TOTAL
    $response = $this->execute(
      $this->SalesService->sales_iva(
        $this->InfoSales->sales_iva($values, $sale_id)
      )
    );

    if ($response)
      return $response;


    // STOCK | PRODUCTS_STOCK
    if (!$withDevolution) {

      $response = $this->execute(
        $this->SalesService->products_stock(
          $this->InfoSales->products_stock($values)
        )
      );
      // Movimiento de Stock
      $response = $this->execute(
        $this->SalesService->stock_movements(
          $this->InfoSales->stock_movements($values, '1', $sale_id)
        )
      );


    } else {

      $response = $this->execute(
        $this->SalesService->devolution_Stock(
          $this->InfoSales->devolution_Stock($values)
        )
      );
      // Movimiento de Stock
      $response = $this->execute(
        $this->SalesService->stock_movements(
          $this->InfoSales->stock_movements($values, '2', $sale_id)
        )
      );
      // Movimiento de Caja
      // BOX_MOVEMENT
      $response = $this->execute(
        $this->SalesService->box_movements(
          $this->InfoSales->box_movements($values, $sale_id, 3)
        )
      );
    }

    if ($response)
      return $response;


    // ACTUALIZAR SECUENCIA
    $sequenceId = (int) $values['point']['sequence_id'];

    if ((int) $values['receipt'] === 1) {
      $this->DocumentSequenceModel->update_last_number($sequenceId);
    } else {
      $this->InvoiceSequenceModel->update_last_number($sequenceId);
    }

    if ($response)
      return $response;

    // PAGO | SALES_PAYMENTS
    // CASH

    if ($withCash) {
      $response = $this->payment_cash($values, $sale_id);
    }
    // CREDIT
    if ($withCredit) {
      $response = $this->payment_credit($values, $sale_id);
    }

    if ($response)
      return $response;


    $db->transComplete();

    if ($db->transStatus() === false) {

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error en la transacción',
        'values' => $values,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    return $this->response->setJSON([
      'status' => true,
      'sale_id' => $sale_id,
      'values' => $values,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }

  private function payment_credit($values, $sale_id)
  {
    // SALES_PAYMENT
    $response = $this->execute(
      $this->SalesService->payment_credit(
        $this->InfoSales->payment_credit($values, $sale_id)
      ),
      $values
    );
    if ($response)
      return $response;


  }


  private function payment_cash($values, $sale_id)
  {
    // SALES_PAYMENT
    $response = $this->execute(
      $this->SalesService->payment_cash(
        $this->InfoSales->payment_cash($values, $sale_id)
      ),
      $values
    );

    // BOX_MOVEMENT
    $response = $this->execute(
      $this->SalesService->box_movements(
        $this->InfoSales->box_movements($values, $sale_id, 1)
      )
    );
    if ($response)
      return $response;

  }

  private function execute($operation, $values = false)
  {
    if (!$operation || !$operation['status']) {

      return $this->json(
        $operation ?: [
          'status' => false,
          'error' => 'Operación no válida'
        ],
        200
      );
    }

    return null;
  }
}