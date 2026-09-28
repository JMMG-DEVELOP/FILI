<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Lista de Impresoras</h5>
      <?php if (can('printer_add')): ?>
        <a href="#" class="btn btn-outline-primary" id="printers_form_new_open">
          <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table_printer">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">IMPRESORA</th>
              <th scope="col">NOMBRE</th>
              <th scope="col">DRIVER</th>
              <th scope="col">PAPER</th>
              <th scope="col">CHARSET</th>
              <?php if (can('printer_view') or can('printer_edit')): ?>
                <?php if (can('printer_view')): ?>
                  <th scope="col">Ver</th>
                <?php endif; ?>
                <?php if (can('printer_edit')): ?>
                  <th scope="col">Editar</th>
                <?php endif; ?>
              <?php endif; ?>
              <?php if (can('printer_delete')): ?>
                <th scope="col">Eliminar</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($values)) { ?>
              <?php foreach ($values as $value) { ?>
                <tr>
                  <th scope="row">
                    <?= esc($value['id']) ?>
                  </th>
                  <td>
                    <?= esc($value['system_name']) ?>
                  </td>
                  <td>
                    <?= esc($value['name']) ?>
                  </td>
                  <td>
                    <?= esc($value['driver_name']) ?>
                  </td>
                  <td>
                    <?= esc($value['paper_name']) ?>
                  </td>
                  <td>
                    <?= esc($value['charset_code']) ?>
                  </td>
                  <?php if (can('printer_view') or can('printer_edit')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-warning printers_form_edit_open"
                        data-id="<?= esc($value['id']) ?>">
                        <?php if (can('printer_edit')): ?>
                          <i class="fas fa-pencil-alt"></i>
                        <?php endif; ?>
                        <?php if (can('printer_view')): ?>
                          <i class="fas fa-angle-double-right"></i>
                        <?php endif; ?>
                      </button>
                    </td>
                  <?php endif; ?>
                  <?php if (can('printer_delete')): ?>
                    <td>
                      <button type="button" class="btn btn-outline-danger printers_form_delete_open"
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