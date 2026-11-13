<?php
namespace App\Controllers\Products\Products\Home;
use App\Controllers\BaseController;



class Prints extends BaseController
{


    public function panel_load()
    {


        $html = view('products/panels/view', );

        return $this->finish(
            true,
            'CARGADO CORRECTAMENTE',
            $html,

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
