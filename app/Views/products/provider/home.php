<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <div class="card">
    <?php $number = 1; ?>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">ADMINISTRACIÓN DE PROVEEDORES</h5>
      <?php if (can('provider_add')): ?>
        <a href="#" class="btn btn-outline-primary" id="provider_form_new_open">
          <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table_provider">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">DESCRIPCIÓN</th>
              <th scope="col">RUC</th>
              <th scope="col">TIMBRADO</th>

              <?php if (can('provider_view') or can('provider_edit')): ?>
                <?php if (can('provider_view')): ?>
                  <th scope="col">Ver</th>
                <?php endif; ?>
                <?php if (can('provider_edit')): ?>
                  <th scope="col">Editar</th>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (can('provider_delete')): ?>
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
                  <td>
                    <?= esc($value['ruc']) ?>
                  </td>
                  <td>
                    <?= esc($value['timbrado']) ?>
                  </td>
                  <?php if (can('provider_view') or can('provider_edit')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-warning provider_form_edit_open"
                        data-id="<?= esc($value['id']) ?>">
                        <?php if (can('provider_edit')): ?>
                          <i class="fas fa-pencil-alt"></i>
                        <?php endif; ?>
                        <?php if (can('provider_view')): ?>
                          <i class="fas fa-angle-double-right"></i>
                        <?php endif; ?>
                      </button>
                    </td>
                  <?php endif; ?>
                  <?php if (can('provider_delete')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-danger provider_form_delete_open"
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