<?php

namespace App\Services;

use App\Models\Box\SalesModel;
use App\Models\Box\SalesDetailsModel;
use App\Models\Box\BoxMovementModel;
use App\Models\Box\DocumentSequenceModel;
use App\Models\Box\InvoiceSequenceModel;
use App\Models\Box\StockMovmentsModel;
use App\Models\Box\SalesPaymentsModel;
use App\Models\Box\SalesIvaModel;
use App\Models\Box\InvoicesModel;
use App\Models\Box\InvoicesSalesModel;

use App\Models\Products\Products\StockModel;

use App\Models\Customer\CustomersCreditsModel;
use App\Models\Customer\CustomersCreditsDetailsModel;
use App\Models\Customer\CreditsSalesDetailsModel;
use App\Models\Customer\CustomerPaymentsModel;




class SalesService
{
  protected $InfoSales;
  protected $SalesModel;
  protected $SalesDetailsModel;
  protected $InvoiceSequenceModel;
  protected $DocumentSequenceModel;
  protected $BoxMovementModel;
  protected $StockMovmentsModel;
  protected $StockModel;
  protected $SalesPaymentsModel;
  protected $SalesIvaModel;


  public function __construct()
  {
    $this->SalesModel = new SalesModel();
    $this->SalesDetailsModel = new SalesDetailsModel();
    $this->InvoiceSequenceModel = new InvoiceSequenceModel();
    $this->DocumentSequenceModel = new DocumentSequenceModel();
    $this->BoxMovementModel = new BoxMovementModel();
    $this->StockMovmentsModel = new StockMovmentsModel();
    $this->StockModel = new StockModel();
    $this->SalesPaymentsModel = new SalesPaymentsModel();
    $this->SalesIvaModel = new SalesIvaModel();


  }

  public function sales($value)
  {
    $response = $this->SalesModel->add_sales($value);

    if (!$response['status']) {
      return $response;
    }

    return [
      'status' => true,
      'sale_id' => $response['id']
    ];
  }

  public function sales_details($value)
  {
    $response = $this->SalesDetailsModel->add_sales_details($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar DETALLES'
      ];
    }

    return [
      'status' => true,
    ];
  }
  public function sales_iva($value)
  {
    $response = $this->SalesIvaModel->add_sales_iva($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar SALES IVA'
      ];
    }

    return [
      'status' => true,
    ];
  }

  // STOCK 
  public function products_stock($value)
  {
    $response = $this->StockModel->discountStock($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'ERROR AL DESCONTAR STOCK'
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function devolution_Stock($values)
  {
    $StockModel = new StockModel();

    if (!$StockModel->devolutionStock($values)) {
      return [
        'status' => false,
        'message' => 'ERROR AL REALIZAR LA DEVOLUCIÓN'
      ];
    }

    return [
      'status' => true
    ];
  }

  public function stock_movements($value)
  {
    $response = $this->StockMovmentsModel->add_stock_movement($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar HISTORIAL DE STOCK',
        'model_errors' => $this->StockMovmentsModel->errors(),
        'db_error' => $this->StockMovmentsModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
    ];
  }
  public function box_movements($value)
  {
    $response = $this->BoxMovementModel->add_box_movement($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar MOVIMIENTO DE CAJA',
        'model_errors' => $this->BoxMovementModel->errors(),
        'db_error' => $this->BoxMovementModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
    ];
  }
  public function multi_payment_cash($value)
  {
    $response = $this->SalesPaymentsModel->add_sales_payment($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar TIPO DE PAGO CASH',
        'model_errors' => $this->SalesPaymentsModel->errors(),
        'db_error' => $this->SalesPaymentsModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
    ];

  }

  public function payment_cash($value)
  {
    $response = $this->SalesPaymentsModel->add_sales_payment($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar TIPO DE PAGO',
        'model_errors' => $this->SalesPaymentsModel->errors(),
        'db_error' => $this->SalesPaymentsModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function payment_digist($value)
  {
    $response = $this->SalesPaymentsModel->add_sales_payment($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar TIPO DE PAGO',
        'model_errors' => $this->SalesPaymentsModel->errors(),
        'db_error' => $this->SalesPaymentsModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function payment_credit($value)
  {
    $CustomersCreditsModel = new CustomersCreditsModel();

    if (!$CustomersCreditsModel->verify_customer_credit_status($value['customer'])) {

      return [
        'status' => false,
        'error' => 'CLIENTE NO ESTA HABILITADO PARA CREDITO'
      ];
    }

    $response = $CustomersCreditsModel->customer_credit($value);

    if (!$response) {

      return [
        'status' => false,
        'error' => 'ERROR AL GUARDAR CREDITO',
        'model_errors' => $CustomersCreditsModel->errors(),
        'db_error' => $CustomersCreditsModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
      'credit' => $response['id']
    ];
  }

  public function payment_credit_detail($values)
  {
    $CustomersCreditsDetailsModel = new CustomersCreditsDetailsModel();

    $id = $CustomersCreditsDetailsModel
      ->add_CustomersCreditsDetailsModel($values);

    if (!$id) {
      return [
        'status' => false,
        'error' => 'ERROR AL GUARDAR DETALLE DEL CREDITO',
        'model_errors' => $CustomersCreditsDetailsModel->errors(),
        'db_error' => $CustomersCreditsDetailsModel->db->error(),
        'data' => $values,
      ];
    }

    return [
      'status' => true,
      'id' => $id
    ];
  }

  public function payment_credit_sales_detail($value)
  {
    $CreditsSalesDetailsModel = new CreditsSalesDetailsModel();

    $response = $CreditsSalesDetailsModel->add_values($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar MOVIMIENTO DE CAJA',
        'model_errors' => $this->BoxMovementModel->errors(),
        'db_error' => $this->BoxMovementModel->db->error(),
        'data' => $value,
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function sales_detail_get($sale_id)
  {
    $SalesDetailsModel = new SalesDetailsModel();

    $values = $SalesDetailsModel->get_id($sale_id);

    return $values;
  }
  public function credit_sales_get($sale_id)
  {
    $CreditsSalesDetailsModel = new CreditsSalesDetailsModel();

    $details = $CreditsSalesDetailsModel->credit_sales_get($sale_id);

    if (empty($details)) {
      return false;
    }

    return $details;
  }
  public function box_movement_get($sale_id)
  {
    $filters = [
      'sales' => $sale_id
    ];

    return $this->BoxMovementModel->getMovements($filters);
  }

  public function box_movement_null($values)
  {
    $response = $this->BoxMovementModel->box_movement_null($values);
    if (!$response) {

      return [
        'status' => false,
        'error' => 'ERROR AL ANULAR MOVIMIENTO DE CAJA',
        'model_errors' => $this->BoxMovementModel->errors(),
        'db_error' => $this->BoxMovementModel->db->error(),
        'data' => $values,
      ];
    }

    return [
      'status' => true,
      // 'credit' => $response['id']
    ];
  }

  public function stock_null($values)
  {
    $StockModel = new StockModel();

    if (!$StockModel->devolutionStock($values)) {
      return [
        'status' => false,
        'message' => 'ERROR AL REALIZAR LA DEVOLUCIÓN'
      ];
    }

    return [
      'status' => true
    ];
  }
  public function box_movement_credit_get($sale_id)
  {
    $BoxMovementModel = new BoxMovementModel();

    $filters = [
      'sales' => $sale_id,
      'type' => 5,
    ];

    $movements = $BoxMovementModel->getMovements($filters);

    if (empty($movements)) {
      return false;
    }

    return $movements;
  }
  public function customer_credit_null($sale_id)
  {
    $CreditsSalesDetailsModel = new CreditsSalesDetailsModel();
    $CustomersCreditsDetailsModel = new CustomersCreditsDetailsModel();
    $CustomersCreditsModel = new CustomersCreditsModel();
    $CustomerPaymentsModel = new CustomerPaymentsModel();

    // ==========================================
    // VERIFICAR SI LA VENTA ES CRÉDITO
    // ==========================================

    $creditSales = $CreditsSalesDetailsModel
      ->credit_sales_get($sale_id);

    /*
     * Si la venta no está registrada en
     * credits_sales_details, no hacemos
     * absolutamente nada relacionado al crédito.
     */

    if (empty($creditSales)) {
      return false;
    }

    // ==========================================
    // OBTENER MOVIMIENTO DE CRÉDITO
    // TYPE = 5
    // ==========================================

    $creditMovement = $this->box_movement_credit_get($sale_id);

    if (empty($creditMovement)) {

      return [
        'status' => false,
        'error' => 'NO SE ENCONTRÓ EL MOVIMIENTO DE CRÉDITO DE LA VENTA'
      ];
    }

    // ==========================================
    // OBTENER MONTO REAL DEL CRÉDITO
    //
    // NO USAMOS EL TOTAL DE LA VENTA.
    //
    // TYPE 5 = CRÉDITO
    // ==========================================

    $amount = 0;

    foreach ($creditMovement as $movement) {

      $amount += (float) $movement['mount'];
    }

    if ($amount <= 0) {

      return [
        'status' => false,
        'error' => 'EL MONTO DEL CRÉDITO ES INVÁLIDO'
      ];
    }

    // ==========================================
    // OBTENER RELACIÓN VENTA / CRÉDITO
    // ==========================================

    /*
     * Normalmente una venta tiene un
     * credit_detail.
     *
     * Tomamos el primer registro para evitar
     * descontar dos veces el mismo monto.
     */

    $creditSale = $creditSales[0];

    $customer = $creditSale['customer'];
    $creditDetailId = $creditSale['credit_detail'];

    // ==========================================
    // OBTENER DETALLE DEL CRÉDITO
    // ==========================================

    $creditDetail = $CustomersCreditsDetailsModel
      ->get_credit_detail($creditDetailId);

    if (!$creditDetail) {

      return [
        'status' => false,
        'error' => 'NO SE ENCONTRÓ EL DETALLE DEL CRÉDITO'
      ];
    }

    // ==========================================
    // OBTENER CRÉDITO DEL CLIENTE
    // ==========================================

    $credit = $CustomersCreditsModel
      ->where('customer', $customer)
      ->first();

    if (!$credit) {

      return [
        'status' => false,
        'error' => 'NO SE ENCONTRÓ EL CRÉDITO DEL CLIENTE'
      ];
    }

    // ==========================================
    // RESTAR MONTO DEL CRÉDITO
    // ==========================================

    $response = $CustomersCreditsModel
      ->customer_credit_null(
        $customer,
        $amount
      );

    if (!$response || !$response['status']) {

      return [
        'status' => false,
        'error' => 'ERROR AL DESCONTAR EL CRÉDITO DEL CLIENTE'
      ];
    }

    // ==========================================
    // REGISTRAR HISTORIAL DEL PAGO
    // ==========================================

    $payment = $CustomerPaymentsModel
      ->add_payment([
        'customer_credit' => $credit['id'],
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'amount' => $amount,
        'user' => session()->get('id')
      ]);

    if (!$payment) {

      return [
        'status' => false,
        'error' => 'ERROR AL REGISTRAR EL HISTORIAL DEL PAGO'
      ];
    }

    // ==========================================
    // RESULTADO
    // ==========================================

    return [
      'status' => true,
      'customer' => $customer,
      'customer_credit' => $credit['id'],
      'credit_detail' => $creditDetailId,
      'amount' => $amount,
      'payment' => $payment
    ];
  }
  public function sales_null($values, $sale_id)
  {
    $SalesModel = new SalesModel();

    $sale = $SalesModel->find($sale_id);

    // ==========================================
    // VERIFICAR QUE LA VENTA EXISTA
    // ==========================================

    if (!$sale) {
      return [
        'status' => false,
        'error' => 'VENTA NO ENCONTRADA'
      ];
    }

    // ==========================================
    // VERIFICAR SI YA ESTÁ ANULADA
    // ==========================================

    if ((int) $sale['status'] === 2) {
      return [
        'status' => false,
        'error' => 'LA VENTA YA SE ENCUENTRA ANULADA'
      ];
    }

    // ==========================================
    // CAMBIAR STATUS
    // 1 = VÁLIDA
    // 2 = ANULADA
    // ==========================================

    $values['status'] = 2;

    $response = $SalesModel->edit_sales(
      $values,
      $sale_id
    );

    if (!$response) {
      return [
        'status' => false,
        'error' => 'ERROR AL MODIFICAR ESTADO DE VENTA'
      ];
    }

    // ==========================================
    // RESPUESTA
    // ==========================================

    return [
      'status' => true,
      'sale_id' => $sale_id,
      'status_sale' => 2
    ];
  }


  public function invoices($values, $saleId)
  {
    $InvoiceModel = new InvoicesModel();
    $InvoiceSalesModel = new InvoicesSalesModel();

    if (empty($saleId)) {
      return [
        'status' => false,
        'error' => 'NO SE RECIBIÓ EL ID DE LA VENTA'
      ];
    }

    // ==========================================
    // GUARDAR FACTURA
    // ==========================================

    $invoiceId = $InvoiceModel->add($values);

    if (!$invoiceId) {

      $dbError = $InvoiceModel->db->error();

      $modelErrors = $InvoiceModel->errors();

      $lastQuery = (string) $InvoiceModel->db->getLastQuery();

      log_message(
        'error',
        'ERROR AL GUARDAR FACTURA: ' . json_encode([
          'data' => $values,
          'model_errors' => $modelErrors,
          'db_error' => $dbError,
          'last_query' => $lastQuery
        ], JSON_UNESCAPED_UNICODE)
      );

      return [
        'status' => false,
        'error' => 'ERROR AL GUARDAR LA FACTURA',
        'model_errors' => $modelErrors,
        'db_error' => $dbError,
        'last_query' => $lastQuery,
        'data' => $values
      ];
    }

    // ==========================================
    // RELACIONAR FACTURA CON VENTA
    // ==========================================

    $relation = $InvoiceSalesModel->add([
      'invoice' => $invoiceId,
      'sales' => $saleId
    ]);

    if (!$relation) {

      log_message(
        'error',
        'ERROR AL RELACIONAR FACTURA: ' . json_encode([
          'invoice_id' => $invoiceId,
          'sale_id' => $saleId,
          'model_errors' => $InvoiceSalesModel->errors(),
          'db_error' => $InvoiceSalesModel->db->error(),
          'last_query' => (string) $InvoiceSalesModel->db->getLastQuery()
        ], JSON_UNESCAPED_UNICODE)
      );

      return [
        'status' => false,
        'error' => 'ERROR AL RELACIONAR LA FACTURA CON LA VENTA',
        'model_errors' => $InvoiceSalesModel->errors(),
        'db_error' => $InvoiceSalesModel->db->error()
      ];
    }

    return [
      'status' => true,
      'invoice_id' => $invoiceId,
      'sale_id' => $saleId
    ];
  }



}
?>