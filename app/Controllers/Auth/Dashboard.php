<?php

namespace App\Controllers\Auth;
use App\Controllers\BaseController;
// use App\Models\Auth\UsersSessionsModel;
use App\Libraries\Infopage;


class Dashboard extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();

            $info = [
                'title' => 'Dashboard',
            ];
            $page = $infopage->infopage($info);
            $render = $infopage->redirect();
            return view($render, $page);
        }



    }


}
