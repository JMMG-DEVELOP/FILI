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

    // INVOICES
    if ((int) ($values['receipt'] ?? 0) === 2) {

      $response = $this->execute(
        $this->SalesService->invoices(
          $this->InfoSales->invoices($values),
          $sale_id
        )
      );

      if ($response) {
        return $response;
      }
    }

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
      $this->InfoSales->box_movements_credit(
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

  // *******
  // ANULACION DE VENTA
  //*********

  public function sales_cash_null()
  {
    return $this->process_sales_null();
  }
  public function sales_credit_null()
  {
    return $this->process_sales_null();
  }

  // public function process_sales_null()
  // {
  //   $values = $this->request->getPost();
  //   $sale_id = $values['id'];

  //   $db = \Config\Database::connect();
  //   $db->transStart();


  //   // Obtener detalles de la venta
  //   $details = $this->SalesService->sales_detail_get($sale_id);
  //   //  Obtener Movimiento
  //   $movement = $this->SalesService->box_movement_get($sale_id);

  //   // Descontar stock
  //   $devolution = $this->stock_null($details);
  //   if ($devolution) {
  //     return $devolution;
  //   }

  //   // Registrar Movimiento de stock
  //   $response = $this->execute(
  //     $this->SalesService->stock_movements(
  //       $this->InfoSales->stock_movements_null($details, $sale_id)
  //     )
  //   );
  //   if ($response) {
  //     return $response;
  //   }
  //   // Registrar Movimiento de Caja

  //   $response = $this->execute(
  //     $this->SalesService->box_movement_null(
  //       $this->InfoSales->box_movements_cash_null($movement, $sale_id)
  //     )
  //   );
  //   if ($response) {
  //     return $response;
  //   }

  //   // Verificar si sales es credito, si lo es, obtener monto del credito anotado 
  //   $response = $this->execute(
  //     $this->SalesService->credit_sales_get($sale_id)
  //   );
  //   if ($response) {



  //   }


  //   // if ($credit) {
  //   //  $credit $this->credit_null($details, $sale_id);
  //   // Descontar monto de customer Credits
  //   // }


  //   // Registrar Operacion en customer_credits_details


  //   // Cambiar status de sales
  //   // $response = $this->execute(
  //   //   $this->SalesService->sales_null(
  //   //     $this->InfoSales->sales_null(),
  //   //     $sale_id
  //   //   )
  //   // );

  //   // if ($response)
  //   //   return $response;
  //   // 

  //   $db->transComplete();

  //   if ($db->transStatus() === false) {

  //     return $this->response->setJSON([
  //       'status' => false,
  //       'error' => 'Error en la transacción',
  //       'values' => $values,
  //       'csrfName' => csrf_token(),
  //       'csrfHash' => csrf_hash()
  //     ]);
  //   }

  //   return $this->response->setJSON([
  //     'status' => true,
  //     'sale' => $movement,
  //     'values' => $details,
  //     'csrfName' => csrf_token(),
  //     'csrfHash' => csrf_hash()
  //   ]);
  // }
  public function process_sales_null()
  {
    $values = $this->request->getPost();
    $sale_id = $values['id'];

    $db = \Config\Database::connect();

    $db->transStart();

    // ==========================================
    // OBTENER DETALLES DE LA VENTA
    // ==========================================

    $details = $this->SalesService
      ->sales_detail_get($sale_id);

    // ==========================================
    // OBTENER MOVIMIENTO DE CAJA
    // ==========================================

    $movement = $this->SalesService
      ->box_movement_get($sale_id);

    // ==========================================
    // RESTAURAR STOCK
    // ==========================================

    $devolution = $this->stock_null($details);

    if ($devolution) {

      $db->transRollback();

      return $devolution;
    }

    // ==========================================
    // REGISTRAR MOVIMIENTO DE STOCK
    // ==========================================

    $response = $this->execute(
      $this->SalesService->stock_movements(
        $this->InfoSales->stock_movements_null(
          $details,
          $sale_id
        )
      )
    );

    if ($response) {

      $db->transRollback();

      return $response;
    }

    // ==========================================
    // REGISTRAR MOVIMIENTO INVERSO DE CAJA
    // ==========================================

    $response = $this->execute(
      $this->SalesService->box_movement_null(
        $this->InfoSales->box_movements_cash_null(
          $movement,
          $sale_id
        )
      )
    );

    if ($response) {

      $db->transRollback();

      return $response;
    }

    // ==========================================
    // VERIFICAR Y REVERTIR CRÉDITO
    // ==========================================

    /*
     * Si la venta no está en
     * credits_sales_details:
     *
     *     devuelve false
     *     NO modifica crédito
     *     NO registra customer_payment
     *
     * Si está registrada:
     *
     *     busca box_movement.type = 5
     *     toma solamente ese monto
     *     descuenta el crédito
     *     registra customer_payment
     */

    $response = $this->SalesService
      ->customer_credit_null($sale_id);

    if ($response !== false && !$response['status']) {

      $db->transRollback();

      return $this->response->setJSON([
        'status' => false,
        'error' => $response['error'],
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    // ==========================================
    // CAMBIAR STATUS DE LA VENTA
    // ==========================================

    $response = $this->execute(
      $this->SalesService->sales_null(
        $this->InfoSales->sales_null(),
        $sale_id
      )
    );

    if ($response) {

      $db->transRollback();

      return $response;
    }

    // ==========================================
    // FINALIZAR TRANSACCIÓN
    // ==========================================

    $db->transComplete();

    // ==========================================
    // VERIFICAR TRANSACCIÓN
    // ==========================================

    if ($db->transStatus() === false) {

      return $this->response->setJSON([
        'status' => false,
        'error' => 'Error en la transacción',
        'values' => $values,
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash()
      ]);
    }

    // ==========================================
    // RESPUESTA
    // ==========================================

    return $this->response->setJSON([
      'status' => true,
      'sale' => $movement,
      'values' => $details,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);
  }


  public function stock_null($values)
  {

    $operation = $this->SalesService->stock_null(
      $this->InfoSales->stock_null($values)
    );

    $response = $this->execute(
      $operation,
      $values
    );


    if ($response) {
      return $response;
    }
  }

  public function credit_null($values, $sale_id)
  {

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