<?php

namespace App\Services;

use App\Models\Box\SalesModel;
use App\Models\Box\SalesDetailsModel;
use App\Models\Box\BoxMovementModel;
use App\Models\Box\DocumentSequenceModel;
use App\Models\Box\InvoiceSequenceModel;
use App\Models\Box\StockMovmentsModel;
use App\Models\Box\SalesPaymentsModel;
use App\Models\Products\Products\StockModel;
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


  public function __construct()
  {
    $this->SalesModel = new SalesModel();
    $this->SalesDetailsModel = new SalesDetailsModel();
    $this->InvoiceSequenceModel = new InvoiceSequenceModel();
    $this->DocumentSequenceModel = new DocumentSequenceModel();
    $this->BoxMovementModel = new BoxMovementModel();
    $this->StockMovmentsModel = new StockMovmentsModel();
    $this->StockModel = new StockModel();
    $this->SalesPaymentsModel =
      new SalesPaymentsModel();
  }

  public function sales($value)
  {
    $response = $this->SalesModel->add_sales($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar cabecera'
      ];
    }

    return [
      'status' => true,
      'sale_id' => $response
    ];
  }

  public function details($value)
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

  public function payment_cash($value)
  {
    $response = $this->SalesPaymentsModel->add_sales_payment($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar TIPO DE PAGO'
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function discountStock($value)
  {
    $response = $this->StockModel->discountStock($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al DESCONTAR STOCK'
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function historyStock($value)
  {
    $response = $this->StockMovmentsModel->add_stock_movement($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar HISTORIAL DE STOCK'
      ];
    }

    return [
      'status' => true,
    ];
  }

  public function boxMovements($value)
  {
    $response = $this->BoxMovementModel->add_box_movement($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar MOVIMIENTO DE CAJA'
      ];
    }

    return [
      'status' => true,
    ];
  }

}
?>