<?php
namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;

use App\Models\Products\Products\ProductModel;
use App\Models\Products\Products\IvaModel;
use App\Models\Products\Brands\BrandsModel;
use App\Models\Products\Section\SectionModel;
use App\Models\Products\Products\SalesModel;

use App\Services\ProductService;




use App\Libraries\ProductsFormatter;


class Edit extends BaseController
{
    public function open()
    {
        $brandsModel = new BrandsModel();
        $sectionModel = new SectionModel();
        $ivaModel = new IvaModel();
        $productModel = new ProductModel();
        $salesModel = new SalesModel();

        // Obtener datos activos (ajusta filtros si hace falta)
        $brands = $brandsModel->findAll();
        $sections = $sectionModel->findAll();
        $iva = $ivaModel->findAll();
        $sales = $salesModel->findAll();

        if ($this->request->isAJAX()) {
            $post = $this->request->getPost();
            $product = $productModel->getByCode($post['code']);
            $form = [
                'title' => 'Editar Producto',
                'brands' => $brands,
                'sections' => $sections,
                'ivas' => $iva,
                'sales' => $sales
            ];

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

        // 2️⃣ 👉 Save Process 
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

    // {
    //     if (!$code) {
    //         return [
    //             'status' => false,
    //             'error' => 'code_empty',
    //             'message' => 'Código vacío'
    //         ];
    //     }

    //     $productModel = new ProductModel();

    //     if ($productModel->getByCode($code)) {
    //         return [
    //             'status' => false,
    //             'error' => 'code_exists',
    //             'message' => 'El código ya existe'
    //         ];
    //     }

    //     return [
    //         'status' => true,
    //         'message' => 'Código disponible',
    //     ];
    // }


}
