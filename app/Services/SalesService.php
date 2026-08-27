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
use App\Models\Products\Products\StockModel;

use App\Models\Customer\CustomersCreditsModel;


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

  // PAGO CASH

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

  public function payment_credit($value)
  {
    $CustomersCreditsModel = new CustomersCreditsModel();

    if ($CustomersCreditsModel->verify_customer_credit_status($value['customer'])) {

      // Puede agregar crédito

    } else {

      return [
        'status' => false,
        'error' => 'CLIENTE NO ESTA HABILITADO PARA CREDITO'
      ];
    }
  }






}
?>