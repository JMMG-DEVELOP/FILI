<?php

namespace App\Controllers\Auth;
use App\Controllers\BaseController;
use App\Models\Auth\UsersSessionsModel;
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

    public function session($user)
    {
        $UsersSessionsModel = new UsersSessionsModel();

        $session = [
            'date' => date("Y-m-d"),
            'start' => date("H:i:s"),
            'user' => $user,
            'device' => 111,
            'ip' => 111,
        ];

        if ($UsersSessionsModel->start($session)) {
            return true;
        }
        return false;
    }


}
