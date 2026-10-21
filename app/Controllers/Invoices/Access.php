<?php
namespace App\Controllers\Invoices;

use App\Controllers\BaseController;
use App\Libraries\Infopage;

class Access extends BaseController
{

  public function index()
  {
    if (session()->get('logged')) {

      $infopage = new Infopage();

      $info = [
        'title' => 'Facturas',

      ];
      $page = $infopage->infopage($info);
      return view('Invoices/index', $page);

    }
  }


}
