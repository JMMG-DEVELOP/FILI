<?php
namespace App\Controllers\Config;

use App\Controllers\BaseController;
use App\Libraries\Infopage;


class Access extends BaseController
{

    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();

            $info = [
                'title' => 'Config',
            ];
            $page = $infopage->infopage($info);
            return view('config/index', $page);

        }
    }
}
