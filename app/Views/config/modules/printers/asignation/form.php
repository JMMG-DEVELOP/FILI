<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?= $title ?> </h5>
      <a href="#" class="btn btn-outline-danger" id="asignation_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_asignation">

        <input type="hidden" id="asignation_id" name="id" value="<?= esc($values['id'] ?? '') ?>">

        <!-- DISPOSITIVO -->
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            DISPOSITIVO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="device" name="device" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php foreach ($devices as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['device'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php } ?>

            </select>
          </div>
        </div>


        <!-- IMPRESORA -->
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            IMPRESORA
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="printer" name="printer" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php foreach ($printers as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['printer'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php } ?>

            </select>
          </div>
        </div>


        <!-- TIPO DE DOCUMENTO -->
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            TIPO DE DOCUMENTO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <select id="type" name="type" class="form-control" required>
              <option value="">
                Seleccione
              </option>

              <?php foreach ($types as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['type'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php } ?>

            </select>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">
      <?php if ($type === 'new') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="asignation_form_new_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>
      <?php if (can('printerAsignation_edit')): ?>
        <?php if ($type === 'edit') { ?>
          <a href="#" class="btn btn-outline-primary px-5" id="asignation_form_edit_save"> <i class="fas fa-save"></i>
          </a>
        <?php } ?>
      <?php endif; ?>
    </div>
  </div>
</div>