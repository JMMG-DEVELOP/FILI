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
        if (session()->get('logged')) {

            $infopage = new Infopage();
            $infobox = new InfoBox();
            $boxModel = new BoxModel();

            if (!session()->get('box')) {

                // Buscar caja abierta del usuario
                $box = $boxModel->get_open_box(session()->get('id'));

                if ($box) {

                    // Recupera la caja existente
                    session()->set([
                        'box' => $box['id']
                    ]);

                } else {

                    // Crear nueva caja
                    $boxData = [
                        'user' => session()->get('id'),
                        'session' => session()->get('session'),
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

            $info = $infobox->info();

            $page = $infopage->infopage($info);
            return view('Box/index', $page);

        }
    }

}
