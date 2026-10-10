<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <br>
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        Lista de Usuarios por Sucursal
      </h5>

      <?php if (can('sucursal_asignation_add')): ?>
        <a href="#" class="btn btn-outline-primary" id="sucursal_asignation_form_new_open">
          <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="card-body">
      <?php
      $grouped = [];

      foreach ($values as $value) {
        $sucursalId = $value['sucursal'];

        if (!isset($grouped[$sucursalId])) {
          $grouped[$sucursalId] = [
            'name' => $value['sucursal_name'],
            'values' => []
          ];
        }

        $grouped[$sucursalId]['values'][] = $value;
      }

      $number = 1;
      ?>

      <?php if (!empty($grouped)): ?>

        <?php foreach ($grouped as $sucursal): ?>

          <div class="mb-4">
            <div class="card-header bg-light">
              <h5 class="mb-0">

                <?= esc($sucursal['name']) ?>
              </h5>
            </div>

            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">USER</th>
                    <th scope="col">NOMBRE</th>
                    <th scope="col">CATEGORIA</th>
                    <?php if (
                      can('sucursal_asignation_view') ||
                      can('sucursal_asignation_edit')
                    ): ?>

                      <?php if (can('sucursal_asignation_view')): ?>
                        <th scope="col">Ver</th>
                      <?php endif; ?>

                      <?php if (can('sucursal_asignation_edit')): ?>
                        <th scope="col">Editar</th>
                      <?php endif; ?>

                    <?php endif; ?>

                    <?php if (can('sucursal_asignation_delete')): ?>
                      <th scope="col">Eliminar</th>
                    <?php endif; ?>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($sucursal['values'] as $value): ?>

                    <tr>
                      <th scope="row">
                        <?= $number++ ?>
                      </th>

                      <td>
                        <?= esc($value['username']) ?>
                      </td>

                      <td>
                        <?= esc($value['user_name']) ?>
                      </td>
                      <td>
                        <?= esc($value['category_name']) ?>
                      </td>

                      <?php if (
                        can('sucursal_asignation_view') ||
                        can('sucursal_asignation_edit')
                      ): ?>

                        <?php if (can('sucursal_asignation_view')): ?>
                          <td>
                            <button type="button" class="btn btn-outline-warning sucursal_asignation_form_edit_open"
                              data-id="<?= esc($value['id']) ?>">
                              <i class="fas fa-angle-double-right"></i>
                            </button>
                          </td>
                        <?php endif; ?>

                        <?php if (can('sucursal_asignation_edit')): ?>
                          <td>
                            <button type="button" class="btn btn-outline-warning sucursal_asignation_form_edit_open"
                              data-id="<?= esc($value['id']) ?>">
                              <i class="fas fa-pencil-alt"></i>
                            </button>
                          </td>
                        <?php endif; ?>

                      <?php endif; ?>

                      <?php if (can('sucursal_asignation_delete')): ?>
                        <td>
                          <button type="button" class="btn btn-outline-danger sucursal_asignation_form_delete_open"
                            data-id="<?= esc($value['id']) ?>" data-name="<?= esc($value['user_name']) ?>">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      <?php endif; ?>

                    </tr>

                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

        <?php endforeach; ?>

      <?php else: ?>

        <div class="text-center py-3">
          NO HAY REGISTROS
        </div>

      <?php endif; ?>

    </div>
  </div>
</div>