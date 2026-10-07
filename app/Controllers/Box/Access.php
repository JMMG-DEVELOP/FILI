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

            return redirect()->to(
                base_url('auth/login')
            );
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
                ->with(
                    'errors',
                    'La sesión no es válida.'
                );
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
         * 4. CARGAR MODELOS
         * =====================================================
         */
        $infopage = new Infopage();
        $infobox = new InfoBox();
        $boxModel = new BoxModel();


        /*
         * =====================================================
         * 5. OBTENER CAJA GUARDADA EN SESSION
         * =====================================================
         */
        $boxId = session()->get('box');


        /*
         * =====================================================
         * 6. SI EXISTE UNA CAJA EN SESSION
         * =====================================================
         */
        if ($boxId) {

            /*
             * Verificar que la caja siga existiendo
             * y que realmente esté abierta.
             */
            $box = $boxModel
                ->where('id', $boxId)
                ->where('user', session()->get('id'))
                ->where('session', $userSessionId)
                ->where('status', 1)
                ->first();


            /*
             * =================================================
             * LA CAJA DE SESSION ES VÁLIDA
             * =================================================
             */
            if ($box) {

                /*
                 * Continuar normalmente.
                 */

            } else {

                /*
                 * La caja guardada en session ya no es válida.
                 *
                 * Eliminamos el ID de caja de la sesión.
                 */
                session()->remove('box');

                $boxId = null;
            }
        }


        /*
         * =====================================================
         * 7. SI NO TENEMOS CAJA VÁLIDA
         * =====================================================
         */
        if (!$boxId) {

            /*
             * Buscar si existe una caja abierta
             * para el usuario actual.
             */
            $box = $boxModel->get_open_box(
                session()->get('id')
            );


            /*
             * =================================================
             * EXISTE UNA CAJA ABIERTA
             * =================================================
             */
            if ($box) {

                /*
                 * Guardar la caja encontrada
                 * en la sesión.
                 */
                session()->set([
                    'box' => $box['id']
                ]);

            } else {

                /*
                 * =================================================
                 * NO EXISTE CAJA ABIERTA
                 * =================================================
                 *
                 * IMPORTANTE:
                 *
                 * Aquí NO se crea la caja.
                 *
                 * Se envía al controlador de apertura:
                 *
                 * GET /box/open
                 *
                 */
                return redirect()->to(
                    base_url('box/open')
                );
            }
        }


        /*
         * =====================================================
         * 8. CARGAR INFORMACIÓN   DE LA CAJA
         * =====================================================
         */
        $info = $infobox->info();

        $page = $infopage->infopage($info);


        /*
         * =====================================================
         * 9. MOSTRAR PANTALLA PRINCIPAL DE BOX
         * =====================================================
         */
        return view(
            'Box/index',
            $page
        );
    }

    public function open()
    {
        if (!session()->get('logged')) {
            return redirect()->to(base_url('auth/login'));
        }

        $infopage = new Infopage();

        $info = [
            'title' => 'Apertura de Caja',
            'type' => 'open'
        ];

        $page = $infopage->infopage($info);

        return view(
            'box/components/open_close',
            $page
        );
    }


}
