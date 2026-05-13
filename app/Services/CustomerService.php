<?php
namespace App\Services;
use App\Models\Customer\CustomersCreditsModel;
use App\Models\Customer\CustomersCreditsDetailsModel;
use App\Models\Customer\CreditsSalesDetailsModel;

class CustomerService
{
  protected $CustomersCreditsModel;
  protected $CustomersCreditsDetailsModel;
  protected $CreditsSalesDetailsModel;

  public function __construct()
  {
    $this->CustomersCreditsModel = new CustomersCreditsModel();
    $this->CustomersCreditsDetailsModel = new CustomersCreditsDetailsModel();
    $this->CreditsSalesDetailsModel = new CreditsSalesDetailsModel();

  }
  public function customer_credits($value)
  {
    $response = $this->CustomersCreditsModel->customer_credit($value);

    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar cabecera'
      ];
    }

    return [
      'status' => true,
      'id' => $response
    ];
  }

  public function customer_credits_details($value)
  {
    $response = $this->CustomersCreditsDetailsModel->add_CustomersCreditsDetailsModel($value);
    if (!$response) {
      return [
        'status' => false,
        'error' => 'Error al guardar cabecera',
        'error_db' => $this->CustomersCreditsDetailsModel->errors(),
        'db' => $this->CustomersCreditsDetailsModel->db->error(),
      ];
    }

    return [
      'status' => true,
      'id' => $response
    ];

  }

  public function credits_sales_details($value)
  {
    $response = $this->CreditsSalesDetailsModel->add_values($value);

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
}