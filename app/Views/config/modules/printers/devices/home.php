<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <div class="card">
    <?php $number = 1; ?>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Lista de Dispositivos</h5>

    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table_devices">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">DESCRIPCIÓN</th>
              <th scope="col">UUID</th>
              <th scope="col">TIPO</th>
              <th scope="col">ESTATUS</th>
              <?php if (can('device_view') or can('device_edit')): ?>
                <?php if (can('device_view')): ?>
                  <th scope="col">Ver</th>
                <?php endif; ?>
                <?php if (can('device_edit')): ?>
                  <th scope="col">Editar</th>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (can('device_delete')): ?>
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
                    <?= esc($value['uuid']) ?>
                  </td>
                  <td>
                    <?= esc($value['type']) ?>
                  </td>
                  <td>
                    <?= esc($value['status_name']) ?>
                  </td>

                  <?php if (can('device_view') or can('device_edit')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-warning devices_form_edit_open"
                        data-id="<?= esc($value['id']) ?>">
                        <?php if (can('device_edit')): ?>
                          <i class="fas fa-pencil-alt"></i>
                        <?php endif; ?>
                        <?php if (can('device_view')): ?>
                          <i class="fas fa-angle-double-right"></i>
                        <?php endif; ?>
                      </button>
                    </td>
                  <?php endif; ?>
                  <?php if (can('device_delete')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-danger devices_form_delete_open"
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