<?php

namespace App\Controllers\Products\Products;

use App\Controllers\BaseController;
use App\Models\Products\Products\ProductDatatableModel;

class Datatable extends BaseController
{
    protected ProductDatatableModel $model;

    public function __construct()
    {
        $this->model = new ProductDatatableModel();
    }

    public function datatable()
    {
        $post = $this->request->getPost();

        $rows = $this->model->getRows($post); // 👈 CORRECTO

        foreach ($rows as &$row) {
            $row['edit'] = '<button class="btn btn-outline-brand product_edit" data-id="' . $row['code'] . '">
                          <i class="fas fa-pencil-alt"></i>
                        </button>';

            $row['delete'] = '<button class="btn btn-outline-danger product_delete" data-id="' . $row['code'] . '">
                            <i class="fas fa-trash"></i>
                          </button>';
        }

        return $this->response->setJSON([
            'draw' => (int) ($post['draw'] ?? 1),
            'recordsTotal' => $this->model->countAll(),
            'recordsFiltered' => $this->model->countFiltered($post),
            'data' => $rows,
            'csrfHash' => csrf_hash(),

        ]);
    }

}
