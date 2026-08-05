<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\UsersSessionsModel;
use App\Models\Box\BoxModel;

class Logout extends BaseController
{
    public function index()
    {
        $session = session();

        // Si no hay sesión iniciada, volver al login
        if (!$session->get('logged')) {
            return redirect()->to(base_url());
        }

        /**
         * Si existe una caja abierta en la sesión,
         * obligar al usuario a cerrarla antes de cerrar sesión.
         */
        if ($session->has('box') && !empty($session->get('box'))) {

            return redirect()->to(base_url('box/close'));
            // Cambia la ruta por la de tu módulo de cierre de caja.
        }

        $sessionId = $session->get('session');

        $db = \Config\Database::connect();
        $db->transStart();

        if (!empty($sessionId)) {

            // Cerrar sesión en la BD
            $usersSessionsModel = new UsersSessionsModel();
            $usersSessionsModel->update($sessionId, [
                'status' => 2,
                'close' => date('Y-m-d H:i:s')
            ]);

            // Cerrar caja por seguridad (si hubiera alguna pendiente)
            $boxModel = new BoxModel();
            $boxModel->close_box_by_session($sessionId);
        }

        $db->transComplete();

        // Si ocurrió un error, no destruir la sesión
        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'No fue posible cerrar la sesión.');
        }

        // Destruir sesión de CodeIgniter
        $session->destroy();

        return redirect()->to(base_url());
    }
}