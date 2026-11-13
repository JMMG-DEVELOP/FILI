<?php
namespace App\Controllers\Products;

use App\Controllers\BaseController;
use App\Libraries\Infopage;

class Access extends BaseController
{
    public function index()
    {
        if (session()->get('logged')) {

            $infopage = new Infopage();

            $info = [
                'title' => 'Productos',
            ];
            $page = $infopage->infopage($info);
            return view('Products/index', $page);
        }
    }

    public function products_panel()
    {
        $html = view(
            'products/panels/products',
        );

        return $this->finish(
            true,
            'CARGADO CORRECTAMENTE',
            $html
        );

    }


    public function finish($status, $msg, $html = '', $data = '')
    {
        return $this->response->setJSON([
            'status' => $status,
            'msg' => $msg,
            'html' => $html,
            'data' => $data,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]);
    }

}
