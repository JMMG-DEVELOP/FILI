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

  public function sales_digist_payment()
  {
    return $this->processSale(false, false, true);

  }

  public function sales_cash_credit_payment()
  {
    return $this->processSale(false, false, false, true);
  }

  public function sales_cash_digist_payment()
  {
    return $this->processSale(false, false, false, false, true);
  }

  public function sales_devolution()
  {
    return $this->processSale(false, false, false, false, false, true);
  }
  private function processSale($withCash = false, $withCredit = false, $withDigist = false, $withCashCredit = false, $withCashDigist = false, $withDevolution = false)
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

    if ($withDigist) {
      $response = $this->payment_digist($values, $sale_id);
    }

    if ($withCashCredit) {
      $response = $this->payment_cash_credit($values, $sale_id);
    }

    if ($withCashDigist) {
      $response = $this->payment_cash_digits($values, $sale_id);
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

  private function payment_cash_digits($values, $sale_id)
  {
    $operation = $this->SalesService->multi_payment_cash(
      $this->InfoSales->multi_payment_cash
      ($values, $sale_id)
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ==========================================
    // BOX_MOVEMENT
    // 
    // ==========================================

    $operation = $this->SalesService->box_movements(
      $this->InfoSales->box_movements_cash_credit_cash(
        $values,
        $sale_id,
        1
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // DIGIST
    $operation = $this->SalesService->payment_digist(
      $this->InfoSales->multi_payment_digist
      ($values, $sale_id)
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ==========================================
    // BOX_MOVEMENT
    // 
    // ==========================================

    $operation = $this->SalesService->box_movements(
      $this->InfoSales->box_movements_cash_digits_digits(
        $values,
        $sale_id,
        1
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }
  }

  private function payment_cash_credit($values, $sale_id)
  {

    $operation = $this->SalesService->multi_payment_cash(
      $this->InfoSales->multi_payment_cash
      ($values, $sale_id)
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ==========================================
    // BOX_MOVEMENT
    // 
    // ==========================================

    $operation = $this->SalesService->box_movements(
      $this->InfoSales->box_movements_cash_credit_cash(
        $values,
        $sale_id,
        1
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }
    // ==========================================
    // CUSTOMER_CREDITS
    // ==========================================

    $operation = $this->SalesService->payment_credit(
      $this->InfoSales->multi_payment_credit($values)
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }
    // ID del crédito creado o actualizado
    $credit_id = $operation['credit'];

    // ==========================================
    // CUSTOMER_CREDIT_DETAILS
    // ==========================================

    $operation = $this->SalesService->payment_credit_detail(
      $this->InfoSales->multi_payment_credit_detail(
        $values,
        $credit_id,
        2
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ID del detalle del crédito
    $credit_detail_id = $operation['id'];

    // ==========================================
    // CREDITS_SALES_DETAILS
    // ==========================================

    $operation = $this->SalesService->payment_credit_sales_detail(
      $this->InfoSales->payment_credit_sales_detail(
        $credit_detail_id,
        $sale_id
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );


    if ($response) {
      return $response;
    }
    // ==========================================
    // BOX_MOVEMENT
    // sales_type = 2 → CREDITO
    // ==========================================

    $operation = $this->SalesService->box_movements(
      $this->InfoSales->box_movements_cash_credit_credit(
        $values,
        $sale_id,
        5
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    return null;


  }
  private function payment_digist($values, $sale_id)
  {
    // SALES_PAYMENT
    $response = $this->execute(
      $this->SalesService->payment_digist(
        $this->InfoSales->payment_digist($values, $sale_id)
      ),
      $values
    );

    if ($response) {
      return $response;
    }

    // BOX_MOVEMENT
    $response = $this->execute(
      $this->SalesService->box_movements(
        $this->InfoSales->box_movements($values, $sale_id, 1)
      )
    );

    if ($response) {
      return $response;
    }

    return null;
  }
  private function payment_credit($values, $sale_id)
  {
    // ==========================================
    // CUSTOMER_CREDITS
    // ==========================================

    $operation = $this->SalesService->payment_credit(
      $this->InfoSales->payment_credit($values)
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ID del crédito creado o actualizado
    $credit_id = $operation['credit'];

    // ==========================================
    // CUSTOMER_CREDIT_DETAILS
    // ==========================================

    $operation = $this->SalesService->payment_credit_detail(
      $this->InfoSales->payment_credit_detail(
        $values,
        $credit_id,
        1
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ID del detalle del crédito
    $credit_detail_id = $operation['id'];

    // ==========================================
    // CREDITS_SALES_DETAILS
    // ==========================================

    $operation = $this->SalesService->payment_credit_sales_detail(
      $this->InfoSales->payment_credit_sales_detail(
        $credit_detail_id,
        $sale_id
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }


    if ($response) {
      return $response;
    }
    // ==========================================
    // BOX_MOVEMENT
    // sales_type = 2 → CREDITO
    // ==========================================

    $operation = $this->SalesService->box_movements(
      $this->InfoSales->box_movements(
        $values,
        $sale_id,
        5
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    return null;
  }


  private function payment_cash($values, $sale_id)
  {
    // ==========================================
    // SALES_PAYMENT
    // ==========================================

    $operation = $this->SalesService->payment_cash(
      $this->InfoSales->payment_cash(
        $values,
        $sale_id
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    // ==========================================
    // BOX_MOVEMENT
    // sales_type = 1 → CONTADO
    // ==========================================

    $operation = $this->SalesService->box_movements(
      $this->InfoSales->box_movements(
        $values,
        $sale_id,
        1
      )
    );

    $response = $this->execute(
      $operation,
      $values
    );

    if ($response) {
      return $response;
    }

    return null;
  }

  private function execute($operation, $values = false)
  {
    if (!$operation || !$operation['status']) {

      return $this->json(
        $operation ?: [
          'status' => false,
          'error' => 'ERROR AL EJECUTAR LA OPERACIÓN'
        ],
        200
      );
    }

    return null;
  }
}