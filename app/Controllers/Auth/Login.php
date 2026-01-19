<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\UsersModel; 
use App\Models\Auth\UsersSucursals; 
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

        $sucursal = $userSucursal->details($user['id']);
        $sucursales = array_column($sucursal, 'sucursal');

        $session = session();
        session()->set([
            'logged'   => true,
            'id'       => $user['id'],
            'user'     => $user['user'],
            'category' => $user['category'],
            'permissions' => $permissions,
             'sucursal' => $sucursales,
        ]);

        return redirect()->to(base_url('dashboard'));
    }


}
