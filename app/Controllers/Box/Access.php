<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\Infopage;
use App\Libraries\InfoBox;




class Access extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();
            $infobox = new InfoBox();

            $info = $infobox->info();

            $page = $infopage->infopage($info);
            return view('Box/index', $page);

        }
    }

}
