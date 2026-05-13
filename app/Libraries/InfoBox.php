<?php

namespace App\Libraries;
use App\Models\Auth\UsersModel;
use App\Models\Box\PaymentTypeModel;
use App\Models\Box\SalesTypeModel;
use App\Models\Box\PercentModel;
use App\Models\Customer\CustomerModel;
use App\Models\Box\BoxMovementTypeModel;






class InfoBox
{
  public function info(): array
  {
    return [

      'title' => 'Caja',
    ];
  }

  public function payment()
  {
    // Instanciar modelos
    $paymentTypeModel = new PaymentTypeModel();
    $salesTypeModel = new SalesTypeModel();
    $cardPercent = new PercentModel();
    $percent = $cardPercent->find();

    // Retornar en un solo array
    return [
      'payment' => $paymentTypeModel->findAll(),
      'sales' => $salesTypeModel->findAll(),
      'percent' => $percent,
    ];

  }

  public function customer()
  {
    $customerModel = new CustomerModel();

  }

  public function box_movements()
  {
    // Instanciar modelos
    $BoxMovementTypeModel = new BoxMovementTypeModel();

    // Retornar en un solo array
    return [
      'type' => $BoxMovementTypeModel->findAll(),

    ];

  }


}


