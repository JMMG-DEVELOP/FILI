<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\InfoSales;
use App\Models\Box\SalesModel;
class Sales extends BaseController
{
  public function sales_cash_payment()
  {
    $InfoSales = new InfoSales();
    $SalesModel = new SalesModel();

    $values = $this->request->getPost();
    $values = $InfoSales->formatter($values);
    $sales_formatter = $InfoSales->sales($values);


    // Retornar producto
    return $this->response->setJSON([
      'status' => true,
      'data' => $sales_formatter,
      'csrfName' => csrf_token(),
      'csrfHash' => csrf_hash()
    ]);


  }

}