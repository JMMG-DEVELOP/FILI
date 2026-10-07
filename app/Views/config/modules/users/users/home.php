<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <div class="card">
    <?php $number = 1; ?>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Lista de Usuarios</h5>
      <?php if (can('user_add')): ?>
        <a href="#" class="btn btn-outline-primary" id="users_form_new_open">
          <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table_users">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">USUARIO</th>
              <th scope="col">NOMBRE</th>
              <th scope="col">CEDULA</th>
              <th scope="col">CELULAR</th>
              <th scope="col">ESTATUS</th>
              <th scope="col">CATEGORIA</th>
              <?php if (can('user_view') or can('user_edit')): ?>
                <?php if (can('user_view')): ?>
                  <th scope="col">Ver</th>
                <?php endif; ?>
                <?php if (can('user_edit')): ?>
                  <th scope="col">Editar</th>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (can('user_delete')): ?>
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
                    <?= esc($value['user']) ?>
                  </td>
                  <td>
                    <?= esc($value['name']) ?>
                  </td>
                  <td>
                    <?= esc($value['ci']) ?>
                  </td>
                  <td>
                    <?= esc($value['phone']) ?>
                  </td>
                  <td>
                    <?= esc($value['status_name']) ?>
                  </td>
                  <td>
                    <?= esc($value['category_name']) ?>
                  </td>
                  <?php if (can('user_view') or can('user_edit')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-warning users_form_edit_open"
                        data-id="<?= esc($value['id']) ?>">
                        <?php if (can('user_edit')): ?>
                          <i class="fas fa-pencil-alt"></i>
                        <?php endif; ?>
                        <?php if (can('user_view')): ?>
                          <i class="fas fa-angle-double-right"></i>
                        <?php endif; ?>
                      </button>
                    </td>
                  <?php endif; ?>

                  <?php if (can('user_delete')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-danger users_form_delete_open"
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


      </div>
    </div>

  </div>
</div>