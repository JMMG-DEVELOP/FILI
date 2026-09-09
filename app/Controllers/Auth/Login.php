<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Auth\UsersModel;
use App\Models\Auth\UsersSucursals;
use App\Models\Auth\UsersSessionsModel;
use App\Models\Auth\DevicesModel;
use Config\Permissions;

class Login extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }


    public function auth()
    {
        $userModel = new UsersModel();

        $rules = [
            'username' => 'required|max_length[20]',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    $this->validator->listErrors()
                );
        }

        $post = $this->request->getPost([
            'username',
            'password'
        ]);

        $user = $userModel->login(
            $post['username'],
            $post['password']
        );

        if ($user === null) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    'Credenciales Incorrectos'
                );
        }


        /*
         * =====================================================
         * CARGAR PERMISOS
         * =====================================================
         */

        $permissionsConfig = new Permissions();

        $permissions =
            $permissionsConfig->details[$user['category']] ?? [];


        /*
         * =====================================================
         * CREAR / RECUPERAR SESIÓN DEL USUARIO
         * =====================================================
         *
         * session() devuelve:
         *
         * [
         *     'session' => ID de users_sessions,
         *     'device'  => ID de devices,
         *     'uuid'    => UUID del dispositivo,
         *     'new_uuid' => true/false
         * ]
         */

        $sessionData = $this->session($user['id']);


        /*
         * =====================================================
         * SESIÓN DE CODEIGNITER
         * =====================================================
         */

        session()->set([
            'logged' => true,
            'id' => $user['id'],
            'user' => $user['user'],
            'category' => $user['category'],
            'permissions' => $permissions,

            /*
             * ID de USERS_SESSIONS
             */
            'session' => $sessionData['session'],

            /*
             * ID de DEVICES
             */
            'device' => $sessionData['device']
        ]);


        /*
         * =====================================================
         * REDIRECCIÓN
         * =====================================================
         */

        $response = redirect()->to(
            base_url('dashboard')
        );


        /*
         * =====================================================
         * GUARDAR UUID DEL DISPOSITIVO
         * =====================================================
         *
         * SOLAMENTE si acabamos de generar uno.
         */

        if ($sessionData['new_uuid']) {

            $response->setCookie([
                'name' => 'device_uuid',
                'value' => $sessionData['uuid'],
                'expire' => 60 * 60 * 24 * 365,
                'path' => '/',
                'domain' => '',
                'secure' => $this->request->isSecure(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }


        return $response;
    }

    /**
     * Crear o recuperar la sesión
     * del usuario en el dispositivo actual.
     */
    public function session($user)
    {
        $UsersSessionsModel = new UsersSessionsModel();
        $DevicesModel = new DevicesModel();

        /*
         * Información del navegador.
         */
        $agent = $this->request->getUserAgent();


        /*
         * =====================================================
         * 1. OBTENER UUID DEL DISPOSITIVO
         * =====================================================
         */

        $deviceUUID = $this->request->getCookie('device_uuid');

        $newUUID = false;


        /*
         * =====================================================
         * 2. VALIDAR UUID
         * =====================================================
         */

        if (
            !$deviceUUID ||
            !preg_match(
                '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
                $deviceUUID
            )
        ) {

            /*
             * Generar UUID solamente cuando
             * realmente no existe uno válido.
             */
            $deviceUUID = $this->generateDeviceUUID();

            $newUUID = true;
        }


        /*
         * =====================================================
         * 3. BUSCAR DISPOSITIVO
         * =====================================================
         */

        $device = $DevicesModel
            ->where('uuid', $deviceUUID)
            ->first();


        /*
         * =====================================================
         * 4. CREAR DISPOSITIVO SI NO EXISTE
         * =====================================================
         */

        if (!$device) {

            $deviceId = $DevicesModel->insert([
                'uuid' => $deviceUUID,

                /*
                 * El administrador podrá colocar
                 * posteriormente Caja 1, Oficina, etc.
                 */
                'name' => null,

                'type' => $agent->isMobile()
                    ? 'Mobile'
                    : 'Desktop',

                'enabled' => 1

            ], true);


            if (!$deviceId) {

                throw new \RuntimeException(
                    'No se pudo registrar el dispositivo.'
                );
            }

        } else {

            /*
             * =================================================
             * DISPOSITIVO EXISTENTE
             * =================================================
             */

            $deviceId = (int) $device['id'];


            /*
             * Verificar que esté habilitado.
             */
            if ((int) $device['enabled'] !== 1) {

                throw new \RuntimeException(
                    'El dispositivo no está habilitado.'
                );
            }
        }


        /*
         * =====================================================
         * 5. BUSCAR SESIÓN ACTIVA
         * =====================================================
         *
         * usuario + dispositivo
         */

        $activeSession = $UsersSessionsModel
            ->where('user', $user)
            ->where('device', $deviceId)
            ->where('status', 1)
            ->first();


        /*
         * =====================================================
         * 6. SESIÓN EXISTENTE
         * =====================================================
         */

        if ($activeSession) {

            return [
                'session' => $activeSession['id'],
                'device' => $deviceId,
                'uuid' => $deviceUUID,
                'new_uuid' => $newUUID
            ];
        }


        /*
         * =====================================================
         * 7. CREAR NUEVA SESIÓN
         * =====================================================
         */

        $data = [

            'date' => date('Y-m-d'),

            'start' => date('H:i:s'),

            'user' => $user,

            /*
             * FK -> DEVICES.id
             */
            'device' => $deviceId,

            'ip' => $this->request->getIPAddress(),

            'browser' =>
                $agent->getBrowser()
                . ' '
                . $agent->getVersion(),

            'type' =>
                $agent->isMobile()
                ? 'Mobile'
                : 'Desktop',

            'user_agent' =>
                $agent->getAgentString(),

            'ip_true' =>
                $_SERVER['HTTP_X_FORWARDED_FOR']
                ?? $_SERVER['REMOTE_ADDR'],

            'device_hint' =>
                $this->getDeviceModel(
                    $agent->getAgentString()
                ),

            'status' => 1
        ];


        $sessionId = $UsersSessionsModel->start($data);


        /*
         * =====================================================
         * 8. VERIFICAR CREACIÓN
         * =====================================================
         */

        if (!$sessionId) {

            throw new \RuntimeException(
                'No se pudo crear la sesión del usuario.'
            );
        }


        /*
         * =====================================================
         * 9. DEVOLVER INFORMACIÓN
         * =====================================================
         */

        return [
            'session' => $sessionId,
            'device' => $deviceId,
            'uuid' => $deviceUUID,
            'new_uuid' => $newUUID
        ];
    }


    /**
     * Obtener modelo aproximado del dispositivo.
     */
    private function getDeviceModel($userAgent)
    {
        if (
            preg_match(
                '/Android.*; ([^;]+)\)/',
                $userAgent,
                $matches
            )
        ) {
            return trim($matches[1]);
        }

        if (
            preg_match(
                '/iPhone|iPad|iPod/',
                $userAgent
            )
        ) {
            return 'Apple Device';
        }

        return 'Unknown';
    }


    /**
     * Generar UUID v4.
     */
    private function generateDeviceUUID()
    {
        $data = random_bytes(16);

        $data[6] = chr(
            (ord($data[6]) & 0x0f) | 0x40
        );

        $data[8] = chr(
            (ord($data[8]) & 0x3f) | 0x80
        );

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(
                bin2hex($data),
                4
            )
        );
    }
}