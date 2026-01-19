<?php
namespace App\Controllers\Products\Brands;

use App\Controllers\BaseController;

use App\Models\Brands\BrandsModel;


class Add extends BaseController
{
    public function open()
    {
        $form = [
            'title' => 'Nueva Marca',
        ];
        $form_html = view('Products/brands/form', $form);


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
