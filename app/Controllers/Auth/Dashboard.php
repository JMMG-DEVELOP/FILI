<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\Infopage;

class Dashboard extends BaseController
{
    public function index()
    {
        /*
         * =====================================================
         * 1. VERIFICAR LOGIN
         * =====================================================
         */

        if (!session()->get('logged')) {
            return redirect()->to(
                base_url('auth/login')
            );
        }


        /*
         * =====================================================
         * 2. CATEGORÍA DEL USUARIO
         * =====================================================
         */

        $category = session()->get('category');


        /*
         * =====================================================
         * 3. CATEGORÍA 4 -> CAJA
         * =====================================================
         *
         * No cargamos ninguna vista de Box directamente.
         *
         * Enviamos a /box para que:
         *
         * /box
         *    ↓
         * Box\Access::index()
         *    ↓
         * ¿Existe caja abierta?
         *    ↓
         * SI  -> Box/index
         * NO  -> Box/open
         *
         */

        if ((string) $category === '4') {

            return redirect()->to(
                base_url('box')
            );
        }


        /*
         * =====================================================
         * 4. DASHBOARD NORMAL
         * =====================================================
         */

        $infopage = new Infopage();

        $info = [
            'title' => 'Dashboard',
        ];

        $page = $infopage->infopage($info);

        return view(
            'dashboard/index',
            $page
        );
    }
}