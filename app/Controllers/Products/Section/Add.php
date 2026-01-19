<?php
namespace App\Controllers\Products\Section;

use App\Controllers\BaseController;

use App\Models\Section\SectionModel;


class Add extends BaseController
{
    public function open()
    {
        $form = [
            'title' => 'Nueva Sección',
        ];
        $form_html = view('Products/section/form', $form);


        return $this->response->setJSON([
            'status' => true,
            'form' => $form_html,
            'data' => '<p>Formulario de marca cargado aquí</p>',
            'csrfHash' => csrf_hash()
        ]);

    }

    public function save()
    {

    }

}
