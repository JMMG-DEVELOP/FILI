<?php
namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;

use App\Models\Products\ProductsModel;
use App\Models\Products\Brands\BrandsModel;
use App\Models\Products\Section\SectionModel;
use App\Models\Products\Products\ProductIvaModel;




class Add extends BaseController
{
    public function open()
    {
        $brandsModel = new BrandsModel();
        $sectionModel = new SectionModel();
        $ivaModel = new ProductIvaModel();

        // Obtener datos activos (ajusta filtros si hace falta)
        $brands = $brandsModel->findAll();
        $sections = $sectionModel->findAll();
        $iva = $ivaModel->findAll();

        $form = [
            'title' => 'Nuevo Producto',
            'brands' => $brands,
            'sections' => $sections,
            'ivas' => $iva
        ];

        $form_html = view('Products/products/form', $form);

        return $this->response->setJSON([
            'status' => true,
            'form' => $form_html,
            'data' => [
                'type' => 'add'
            ],
            'csrfHash' => csrf_hash()
        ]);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => false,
                    'message' => 'Acceso no permitido',
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]);
        }

        // 👉 AQUÍ IRÁ TU LÓGICA DE GUARDADO REAL

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Producto guardado correctamente',
            'data' => [
                'type' => 'add'
            ],
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]);

    }

}
