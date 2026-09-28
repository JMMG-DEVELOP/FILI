<?php
namespace App\Controllers\Config;

use App\Controllers\BaseController;
use App\Libraries\Infopage;
use App\Models\Auth\DevicesModel;
use App\Models\Auth\UsersSessionsModel;

class Access extends BaseController
{

    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();
            $devicesModel = new DevicesModel;
            $data = $devicesModel->getDeviceSession(session()->get('session'));

            $info = [
                'title' => 'Config',
                'session' => $data
            ];
            $page = $infopage->infopage($info);
            return view('config/index', $page);

        }
    }

    public function delete_open()
    {
        $values = $this->request->getPost();

        $data = [
            'id' => $values['id'],
            'name' => $values['name'],
            'type' => $values['type']
        ];
        $html = view('Config/components/delete', $data);

        return $this->response->setJSON([
            'status' => true,
            'html' => $html,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]);

    }

}
