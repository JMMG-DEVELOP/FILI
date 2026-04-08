<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\UsersModel;
use App\Models\Auth\UsersSucursals;
use App\Models\Auth\UsersSessionsModel;
use Config\Permissions;

class Login extends BaseController
{
    public function index()
    {
        // return password_hash('12345', PASSWORD_DEFAULT);
        return view('auth/login');
    }

    public function auth()
    {
        $userModel = new UsersModel();
        $userSucursal = new UsersSucursals();

        $rules = [
            'username' => 'required|max_length[20]',
            'password' => 'required'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->listErrors());
        }

        $post = $this->request->getPost(['username', 'password']);

        $user = $userModel->login($post['username'], $post['password']);

        if ($user === null) {
            return redirect()->back()->withInput()->with('errors', 'Credenciales Incorrectos');

        }

        // Cargamos config
        $permissionsConfig = new Permissions();

        // Obtenemos permisos según categoría
        $permissions = $permissionsConfig->details[$user['category']] ?? [];

        $usersession = $this->session($user['id']);

        $session = session();
        session()->set([
            'logged' => true,
            'id' => $user['id'],
            'user' => $user['user'],
            'category' => $user['category'],
            'permissions' => $permissions,
            'session' => $usersession
        ]);

        return redirect()->to(base_url('dashboard'));
    }
    public function session($user)
    {
        $session = session();

        // Si ya hay sesión activa en PHP, devolverla
        if ($session->get('logged') && $session->get('session')) {
            return $session->get('session');
        }

        // Si no hay sesión → crear nueva en BD
        $UsersSessionsModel = new UsersSessionsModel();

        $agent = $this->request->getUserAgent();

        $data = [
            'date' => date("Y-m-d"),
            'start' => date("H:i:s"),
            'user' => $user,
            'device' => $agent->getPlatform(),
            'ip' => $this->request->getIPAddress(),
            'browser' => $agent->getBrowser() . ' ' . $agent->getVersion(),
            'type' => $agent->isMobile() ? 'Mobile' : 'Desktop',
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'ip_true' => $_SERVER['HTTP_X_FORWARDED_FOR']
                ?? $_SERVER['REMOTE_ADDR'],
            'device_hint' => $this->getDeviceModel($agent->getAgentString()),
            'status' => 1
        ];

        return $UsersSessionsModel->start($data);
    }

    function getDeviceModel($userAgent)
    {
        if (preg_match('/Android.*; ([^;]+)\)/', $userAgent, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/iPhone|iPad|iPod/', $userAgent)) {
            return 'Apple Device';
        }

        return 'Unknown';
    }



}
