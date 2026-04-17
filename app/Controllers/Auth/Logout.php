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

                // 🔹 Cerrar sesión usuario
                $UsersSessionsModel = new UsersSessionsModel();
                $UsersSessionsModel->update($sessionId, [
                    'status' => 2,
                    'close' => date("Y-m-d H:i:s")
                ]);

                // 🔹 Cerrar caja
                $BoxModel = new \App\Models\Box\BoxModel();
                $BoxModel->close_box_by_session($sessionId);
            }

            $session->destroy();
        }

        return redirect()->to(base_url());
    }
}