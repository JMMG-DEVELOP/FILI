<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <?= esc($title) ?>
      </h5>

      <a href="#" class="btn btn-outline-danger" id="sucursal_asignation_form_close">
        <i class="fas fa-times"></i>
      </a>
    </div>

    <div class="card-body">
      <form id="form_sucursal_asignation">

        <input type="hidden" id="sucursal_asignation_id" name="sucursal_asignation_id"
          value="<?= esc($values['id'] ?? '') ?>">

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right" for="user">
            USUARIO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="user" name="user" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php foreach ($users as $item): ?>

                <option value="<?= esc($item['id']) ?>" <?= (
                    (string) ($values['user'] ?? '') ===
                    (string) $item['id']
                  ) ? 'selected' : '' ?>>
                  <?= esc($item['user']) ?>
                  -
                  <?= esc($item['name']) ?>
                </option>

              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right" for="sucursal">
            SUCURSAL
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="sucursal" name="sucursal" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php foreach ($sucursals as $item): ?>

                <option value="<?= esc($item['id']) ?>" <?= (
                    (string) ($values['sucursal'] ?? '') ===
                    (string) $item['id']
                  ) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>

              <?php endforeach; ?>
            </select>
          </div>
        </div>

      </form>
    </div>

    <div class="card-footer d-flex justify-content-end align-items-center">

      <?php if ($type === 'new'): ?>

        <a href="#" class="btn btn-outline-primary px-5" id="sucursal_asignation_form_new_save">
          <i class="fas fa-save"></i>
        </a>

      <?php endif; ?>

      <?php if (
        $type === 'edit' &&
        can('sucursal_asignation_edit')
      ): ?>

        <a href="#" class="btn btn-outline-primary px-5" id="sucursal_asignation_form_edit_save">
          <i class="fas fa-save"></i>
        </a>

      <?php endif; ?>

    </div>
  </div>
</div>