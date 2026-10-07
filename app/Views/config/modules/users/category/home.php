<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <div class="card">
    <?php $number = 1; ?>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Lista de Categorias Usuarios</h5>
      <?php if (can('category_add')): ?>
        <a href="#" class="btn btn-outline-primary" id="category_form_new_open">
          <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table_category">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">DESCRIPCIÓN</th>
              <?php if (can('category_view') or can('category_edit')): ?>
                <?php if (can('category_view')): ?>
                  <th scope="col">Ver</th>
                <?php endif; ?>
                <?php if (can('category_edit')): ?>
                  <th scope="col">Editar</th>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (can('category_delete')): ?>
                <th scope="col">Eliminar</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($values)) { ?>
              <?php foreach ($values as $value) { ?>
                <tr>
                  <th scope="row">
                    <?= $number++ ?>
                  </th>
                  <td>
                    <?= esc($value['name']) ?>
                  </td>

                  <?php if (can('category_view') or can('category_edit')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-warning category_form_edit_open"
                        data-id="<?= esc($value['id']) ?>">
                        <?php if (can('category_edit')): ?>
                          <i class="fas fa-pencil-alt"></i>
                        <?php endif; ?>
                        <?php if (can('category_view')): ?>
                          <i class="fas fa-angle-double-right"></i>
                        <?php endif; ?>
                      </button>
                    </td>
                  <?php endif; ?>
                  <?php if (can('category_delete')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-danger category_form_delete_open"
                        data-id="<?= esc($value['id']) ?>" data-name="<?= esc($value['name']) ?>">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>

        <?php if (empty($values)) { ?>
          <div class="text-center py-3">
            NO HAY REGISTROS
          </div>
        <?php } ?>
      </div>
    </div>

  </div>
</div>