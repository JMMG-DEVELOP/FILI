<?php
namespace App\Controllers\Products\Products\Home;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductDatatableModel;
use App\Models\Products\Products\ProductModel;


class Home extends BaseController
{
    protected $ProductDatatableModel;
    public function __construct()
    {
        $this->ProductDatatableModel = new ProductDatatableModel();
    }

    public function panel_load()
    {
        $permissions = session()->get('permissions') ?? [];

        $data = [
            'canViewCost' => can('product_cost_view'),
            'canViewProduct' => can('product_product_view'),
            'canEditProduct' => can('product_product_edit'),
            'canDeleteProduct' => can('product_product_delete'),
            'canAddProduct' => can('product_product_add'),
        ];

        $html = view('products/products/home/home', $data);

        return $this->finish(
            true,
            'CARGADO CORRECTAMENTE',
            $html,
            $data
        );
    }
    public function datatable()
    {
        $post = $this->request->getPost();

        $rows = $this->ProductDatatableModel->getRows($post);

        foreach ($rows as &$row) {

            $code = esc($row['code']);

            // EDITAR
            $row['edit'] = '
            <button
                type="button"
                class="btn btn-outline-brand product_edit"
                data-code="' . $code . '"
                title="Editar producto">

                <i class="fas fa-pencil-alt"></i>

            </button>
        ';

            // ELIMINAR
            $row['delete'] = '
            <button
                type="button"
                class="btn btn-outline-danger product_delete"
                data-code="' . $code . '"
                title="Eliminar producto">

                <i class="fas fa-trash"></i>

            </button>
        ';

            // VER HISTORIAL
            $row['view'] = '
            <button
                type="button"
                class="btn btn-outline-info product_history"
                data-code="' . $code . '"
                title="Ver historial">

                <i class="fas fa-history"></i>

            </button>
        ';

            // PRINT
            $row['print'] = '
            <button
                type="button"
                class="btn btn-outline-secondary product_print"
                data-code="' . $code . '"
                title="Buscar e imprimir">

                <i class="fas fa-print"></i>

            </button>
        ';
        }

        unset($row);

        return $this->response->setJSON([

            'draw' => (int) ($post['draw'] ?? 1),

            'recordsTotal' =>
                $this->ProductDatatableModel->countAll(),

            'recordsFiltered' =>
                $this->ProductDatatableModel->countFiltered($post),

            'data' => $rows,

            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),

        ]);
    }


    public function updateInlineField()
    {
        $productId = (int) $this->request->getPost('product_id');
        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');

        if ($productId <= 0 || $field === null || $value === null) {
            return $this->response->setJSON([
                'status' => false,
                'error' => 'Datos incompletos.'
            ]);
        }

        $productModel = new ProductModel();

        $result = $productModel->updateInlineField(
            $productId,
            $field,
            $value
        );

        return $this->response->setJSON($result);
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
