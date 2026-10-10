<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <?= esc($title) ?>
      </h5>

      <a href="#" class="btn btn-outline-danger" id="point_asignation_form_close">
        <i class="fas fa-times"></i>
      </a>
    </div>

    <div class="card-body">
      <form id="form_point_asignation">
        <input type="hidden" id="point_asignation_id" name="point_asignation_id"
          value="<?= esc($values['id'] ?? '') ?>">

        <!-- DISPOSITIVO -->
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right" for="device">
            DISPOSITIVO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="device" name="device" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php
              $selectedDevice = (string) ($values['device'] ?? '');
              $deviceEncontrado = false;
              ?>

              <?php foreach ($device as $item): ?>
                <?php
                $deviceId = (string) $item['id'];
                $selected = ($selectedDevice === $deviceId);

                if ($selected) {
                  $deviceEncontrado = true;
                }
                ?>

                <option value="<?= esc($item['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php endforeach; ?>

              <?php
              /*
               * Si el dispositivo actual está deshabilitado,
               * pero ya existe la asignación, lo mostramos
               * para conservar el valor actual al editar.
               */
              ?>

              <?php if (
                $type === 'edit' &&
                $selectedDevice !== '' &&
                !$deviceEncontrado
              ): ?>
                <option value="<?= esc($selectedDevice) ?>" selected>
                  <?= esc(
                    $values['device_name'] ??
                    ('Dispositivo ' . $selectedDevice)
                  ) ?>
                  (DESHABILITADO)
                </option>
              <?php endif; ?>
            </select>
          </div>
        </div>

        <!-- PUNTO DE EXPEDICIÓN -->
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right" for="expedition_point">
            PUNTO DE EXPEDICIÓN
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="expedition_point" name="expedition_point" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php
              $selectedPoint = (string) (
                $values['expedition_point'] ?? ''
              );
              $pointEncontrado = false;
              ?>

              <?php foreach ($expedition_point as $item): ?>
                <?php
                $pointId = (string) $item['id'];
                $selected = ($selectedPoint === $pointId);

                if ($selected) {
                  $pointEncontrado = true;
                }
                ?>

                <option value="<?= esc($item['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                  <?= esc($item['code']) ?>
                  -
                  <?= esc($item['sucursal_name']) ?>
                </option>
              <?php endforeach; ?>

              <?php
              /*
               * Conserva el punto actual si ya no aparece
               * en la lista disponible.
               */
              ?>

              <?php if (
                $type === 'edit' &&
                $selectedPoint !== '' &&
                !$pointEncontrado
              ): ?>
                <option value="<?= esc($selectedPoint) ?>" selected>
                  <?= esc(
                    $values['expedition_point_code'] ?? ''
                  ) ?>
                  -
                  <?= esc($values['sucursal_name'] ?? '') ?>
                </option>
              <?php endif; ?>
            </select>
          </div>
        </div>
      </form>
    </div>

    <div class="card-footer d-flex justify-content-end align-items-center">
      <?php if ($type === 'new'): ?>
        <a href="#" class="btn btn-outline-primary px-5" id="point_asignation_form_new_save">
          <i class="fas fa-save"></i>
        </a>
      <?php endif; ?>

      <?php if (
        $type === 'edit' &&
        can('point_asignation_edit')
      ): ?>
        <a href="#" class="btn btn-outline-primary px-5" id="point_asignation_form_edit_save">
          <i class="fas fa-save"></i>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>