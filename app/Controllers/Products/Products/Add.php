<?php
namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;

use App\Models\Products\Products\ProductModel;
use App\Models\Products\Brands\BrandsModel;
use App\Models\Products\Section\SectionModel;
use App\Models\Products\Products\ProductIvaModel;
use App\Libraries\Products;


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
        $prepare = new Products();

        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => false,
                    'message' => 'ERROR 403',
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]);
        }

        $post = $this->request->getPost();

        // 1️⃣ Validar código
        $validation = $this->validate_code($post['code'] ?? null);

        if ($validation['status'] === false) {
            return $this->response->setJSON(
                array_merge($validation, [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ])
            );
        }

        // 2️⃣ 👉 AQUÍ recién va el proceso de guardado real
        // $productModel->insert(...);
        $data = $prepare->prepare_table_prices($post);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Producto guardado correctamente',
            'data' => $data,
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]);
    }

    public function code_verify()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $result = $this->validate_code(
            $this->request->getPost('code')
        );

        return $this->response->setJSON(
            array_merge($result, [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ])
        );
    }

    private function validate_code($code)
    {
        if (!$code) {
            return [
                'status' => false,
                'error' => 'code_empty',
                'message' => 'Código vacío'
            ];
        }

        $productModel = new ProductModel();

        if ($productModel->getByCode($code)) {
            return [
                'status' => false,
                'error' => 'code_exists',
                'message' => 'El código ya existe'
            ];
        }

        return [
            'status' => true,
            'message' => 'Código disponible',
        ];
    }


}
