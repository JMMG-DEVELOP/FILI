<?php
namespace App\Controllers\Customer;
use App\Libraries\Infopage;

use App\Controllers\BaseController;
class Access extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();
            $info = [
                'title' => 'Customer',
                // 'session' => $data
            ];
            $page = $infopage->infopage($info);
            return view('Customer/index', $page);

        }
    }

}
