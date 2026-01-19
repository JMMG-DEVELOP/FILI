<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class Logout extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {
            session()->destroy();
        }

        return redirect()->to(base_url());
    }
}
