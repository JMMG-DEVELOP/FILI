<?php

namespace App\Libraries;
use App\Models\Auth\UsersModel;
use App\Models\Box\PaymentTypeModel;
use App\Models\Box\SalesTypeModel;
use App\Models\Box\CardPercentModel;




class InfoBox
{
  public function info(): array
  {
    // Instanciar modelos
    $paymentTypeModel = new PaymentTypeModel();
    $salesTypeModel = new SalesTypeModel();
    $cardPercent = new CardPercentModel();
    $percent = $cardPercent->find();

    // Retornar en un solo array
    return [
      'payment' => $paymentTypeModel->findAll(),
      'sales' => $salesTypeModel->findAll(),
      'percent' => $percent[0]['cant'],
      'title' => 'Caja',
    ];
  }


}


