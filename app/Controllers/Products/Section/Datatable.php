<?php

namespace App\Controllers\Products\Section;

use App\Controllers\BaseController;
use App\Models\Products\Section\SectionDatatableModel;

class Datatable extends BaseController
{
  public function datatable()
  {
    $model = new SectionDatatableModel();
    $post = $this->request->getPost();

    $data = [];
    $start = (int) ($post['start'] ?? 0);

    foreach ($model->getRows($post) as $row) {
      $start++;

      $data[] = [
        'number' => $start,
        'name' => esc($row['name']),
        'edit' => '<button class="btn btn-outline-brand section_edit" data-id="' . $row['id'] . '"> <i class="fas fa-pencil-alt"></i> </button>',
        'delete' => '<button class="btn btn-outline-danger section_delete" data-id="' . $row['id'] . '">  <i class="fas fa-trash"></i> </button>',
      ];
    }

    return $this->response->setJSON([
      'draw' => (int) $post['draw'],
      'recordsTotal' => $model->countAll(),
      'recordsFiltered' => $model->countFiltered($post),
      'data' => $data,
      'csrfHash' => csrf_hash()
    ]);
  }
}
