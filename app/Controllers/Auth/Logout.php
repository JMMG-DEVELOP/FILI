<?php
namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\UsersSessionsModel;

class Logout extends BaseController
{
    public function index()
    {
        $session = session();

        if ($session->get('logged')) {

            $sessionId = $session->get('session');

            if ($sessionId) {
                $UsersSessionsModel = new UsersSessionsModel();

                $UsersSessionsModel->update($sessionId, [
                    'status' => 2, // cerrado
                    'close' => date("Y-m-d H:i:s")
                ]);
            }

            $session->destroy();
        }

        return redirect()->to(base_url());
    }
}