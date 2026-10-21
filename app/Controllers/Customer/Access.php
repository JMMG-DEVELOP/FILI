<?php
namespace App\Controllers\Customer;
use App\Libraries\Infopage;

use App\Controllers\BaseController;
class Access extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();
            $info = [
                'title' => 'Customer',
                // 'session' => $data
            ];
            $page = $infopage->infopage($info);
            return view('Customer/index', $page);

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
        $html = view('Customer/components/delete', $data);

        return $this->response->setJSON([
            'status' => true,
            'html' => $html,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]);

    }



}
