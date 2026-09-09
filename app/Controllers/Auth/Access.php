<?php
namespace App\Controllers\Box;

use App\Controllers\BaseController;
use App\Libraries\Infopage;
use App\Libraries\InfoBox;
use App\Models\Box\BoxModel;




class Access extends BaseController
{

    public function index()
    {
        /*
         * =====================================================
         * 1. VERIFICAR LOGIN
         * =====================================================
         */
        if (!session()->get('logged')) {
            return redirect()->to(base_url('auth/login'));
        }


        /*
         * =====================================================
         * 2. VERIFICAR SESIÓN DE USUARIO
         * =====================================================
         */
        $userSessionId = session()->get('session');

        if (!$userSessionId) {

            session()->destroy();

            return redirect()
                ->to(base_url('auth/login'))
                ->with('errors', 'La sesión no es válida.');
        }


        /*
         * =====================================================
         * 3. VERIFICAR QUE USERS_SESSIONS SIGA ACTIVA
         * =====================================================
         */
        $UsersSessionsModel = new \App\Models\Auth\UsersSessionsModel();

        $userSession = $UsersSessionsModel
            ->where('id', $userSessionId)
            ->where('user', session()->get('id'))
            ->where('status', 1)
            ->first();


        /*
         * =====================================================
         * SESIÓN VENCIDA / CERRADA
         * =====================================================
         */
        if (!$userSession) {

            /*
             * Limpiar datos de la sesión de CodeIgniter.
             */
            session()->destroy();

            return redirect()
                ->to(base_url('auth/login'))
                ->with(
                    'errors',
                    'Tu sesión ha finalizado. Inicia sesión nuevamente.'
                );
        }


        /*
         * =====================================================
         * 4. SESIÓN VÁLIDA -> CONTINUAR CON BOX
         * =====================================================
         */

        $infopage = new Infopage();
        $infobox = new InfoBox();
        $boxModel = new BoxModel();


        /*
         * =====================================================
         * 5. RECUPERAR / CREAR CAJA
         * =====================================================
         */
        if (!session()->get('box')) {

            /*
             * Buscar caja abierta del usuario.
             */
            $box = $boxModel->get_open_box(
                session()->get('id')
            );


            if ($box) {

                /*
                 * Recuperar caja existente.
                 */
                session()->set([
                    'box' => $box['id']
                ]);

            } else {

                /*
                 * Crear nueva caja asociada
                 * a la sesión actual.
                 */
                $boxData = [
                    'user' => session()->get('id'),
                    'session' => $userSessionId,
                    'status' => 1
                ];

                $box = $boxModel->add_box($boxData);

                if ($box) {

                    session()->set([
                        'box' => $box
                    ]);
                }
            }
        }


        /*
         * =====================================================
         * 6. CARGAR INFORMACIÓN
         * =====================================================
         */
        $info = $infobox->info();

        $page = $infopage->infopage($info);

        return view('Box/index', $page);
    }
}
