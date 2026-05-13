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
    return $this->processSale();
  }

  public function sales_cash_credit_payment()
  {
    return $this->processSale(true);
  }

  public function sales_procedures_other_payment()
  {
    return $this->processSale(false, true);
  }

  private function processSale($withCredit = false, $whitOther = false)
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

      // CABECERA
      // Actualizar Monto total del credito del cliente
      $customer_credits = $this->InfoSales->customers_credits_diferencce($values);

      $customer_credits_operation = $this->CustomerService->customer_credits($customer_credits);

      if (!$customer_credits_operation['status']) {
        return $this->json($customer_credits_operation, 400);
      }

      $customer_credits_id = $customer_credits_operation['id'];

      // CUERPO DEL CREDITO
      // dETALLES DE LA ANOTACION EN CREDITO
      // 2 saldo por compra
      $customer_credits_details = $this->InfoSales->customer_credits_details($values, $customer_credits_id, 2);

      $customer_credits_details_operation = $this->CustomerService->customer_credits_details($customer_credits_details);

      if (!$customer_credits_details_operation['status']) {
        return $this->json($customer_credits_operation, 400);
      }

      $customer_credits_details_id = $customer_credits_details_operation['id'];

      // Sale a de la que depende el detalle

      // DETALLE
      $response = $this->execute(
        $this->CustomerService->credits_sales_details(
          $this->InfoSales->credits_sales_details($customer_credits_details_id, $sale_id)
        )
      );

      if ($response)
        return $response;
    } //Credit

    // OTHER
    if ($whitOther) {
      // // PAGO
      // $response = $this->execute(
      //   $this->SalesService->payment_cash(
      //     $this->InfoSales->sales_other_payment($values, $sale_id)
      //   )
      // );

      // if ($response)
      //   return $response;

      // MOVIMIENTO DE CAJA
      $response = $this->execute(
        $this->SalesService->boxMovements(
          $this->InfoSales->box_movements_other($values, $sale_id)
        )
      );

      if ($response)
        return $response;
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
      'values' => $values,
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