<?php
namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;

use App\Services\ProductService;
use App\Services\OpenForm;

use App\Libraries\ProductsFormatter;


class Add extends BaseController
{
    public function open()
    {
        $open = new OpenForm();
        $form = $open->form_products('Nuevo Producto');
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
        $productFormatter = new ProductsFormatter();
        $productService = new ProductService();

        $post = $this->request->getPost();

        // 1️⃣ Validar código
        $validation = $productService->validate_code($post['code'] ?? null);
        if ($validation['status'] === false) {
            return $this->response->setJSON(
                array_merge($validation, [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ])
            );
        }

        // 2️⃣ 👉 Save Process 
        $formatter = $productFormatter->tables_formatters($post);

        if ($productService->save($formatter)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Producto guardado correctamente',
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error al Guardar Producto',
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]);
        }

    }

    public function code_verify()
    {
        $productService = new ProductService();

        $result = $productService->validate_code(
            $this->request->getPost('code')
        );

        return $this->response->setJSON(
            array_merge($result, [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ])
        );
    }


}
