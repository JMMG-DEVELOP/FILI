<?php
namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductModel;
use App\Services\ProductService;
use App\Services\OpenForm;

use App\Libraries\ProductsFormatter;


class Edit extends BaseController
{
    public function open()
    {

        $productModel = new ProductModel();
        $open = new OpenForm();

        if ($this->request->isAJAX()) {
            $post = $this->request->getPost();
            $product = $productModel->getByCode($post['code']);
            $form = $open->form_products('Nuevo Producto');
            $form_html = view('Products/products/form', $form);


            $form_html = view('Products/products/form', $form);

            return $this->response->setJSON([
                'status' => true,
                'form' => $form_html,
                'data' => [
                    'type' => 'edit',
                    'product' => $product
                ],
                'csrfHash' => csrf_hash()
            ]);

        }

    }

    public function save()
    {
        $productFormatter = new ProductsFormatter();
        $productService = new ProductService();

        $post = $this->request->getPost();

        // 2️⃣  Save Process 
        $formatter = $productFormatter->tables_formatters($post);

        if ($productService->edit($formatter)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Producto editado correctamente',
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Error al Editar Producto',
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]);
        }

    }

    public function code_verify()
    {
        $productService = new ProductService();
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

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
