<?php

namespace App\Services;

use App\Models\Products\Products\IvaModel;
use App\Models\Products\Brands\BrandsModel;
use App\Models\Products\Section\SectionModel;
use App\Models\Products\Products\SalesModel;
use App\Models\Products\Products\CardPercentModel;

class OpenForm
{

  function form_products($title)
  {
    $brandsModel = new BrandsModel();
    $sectionModel = new SectionModel();
    $ivaModel = new IvaModel();
    $salesModel = new SalesModel();
    $cardPercentModel = new CardPercentModel();
    return [
      'title' => $title,
      'brands' => $brandsModel->findAll(),
      'sections' => $sectionModel->findAll(),
      'ivas' => $ivaModel->findAll(),
      'sales' => $salesModel->findAll(),
      'card_percent' => $cardPercentModel
        ->select('cant')
        ->find(1)['cant'] ?? 5
    ];
  }

}
