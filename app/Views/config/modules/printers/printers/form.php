<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?= $title ?> </h5>
      <a href="#" class="btn btn-outline-danger" id="printers_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_printer">
        <input type="hidden" id="printer_id" name="id" value="<?= esc($values['id'] ?? '') ?>">
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            NOMBRE
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input id="name" name="name" type="text" class="form-control" value="<?= esc($values['name'] ?? '') ?>"
              required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            IMPRESORA
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input id="system_name" name="system_name" type="text" class="form-control minusc"
              value="<?= esc($values['system_name'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            AUTO-CORTE
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" id="auto_cut" name="auto_cut" value="1"
                <?= !empty($values['auto_cut']) ? 'checked' : '' ?>>
              <span class="custom-control-label" id="auto_cut_text">
                <?= !empty($values['auto_cut']) ? 'HABILITADO' : 'NO HABILITADO' ?>
              </span>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CHARSET
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <select id="charset" name="charset" class="form-control" required>
              <option value="">
                Seleccione
              </option>
              <?php foreach ($charset as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['charset'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['code']) ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            DRIVER
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <select id="driver" name="driver" class="form-control" required>
              <option value="">
                Seleccione
              </option>
              <?php foreach ($drivers as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['driver'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            PAPEL
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <select id="paper" name="paper" class="form-control" required>
              <option value="">
                Seleccione
              </option>
              <?php foreach ($paper as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['paper'] ?? '') == $item['id']) ? 'selected' : '' ?>>
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
        <a href="#" class="btn btn-outline-primary px-5" id="printers_form_new_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>
      <?php if (can('printer_edit')): ?>
        <?php if ($type === 'edit') { ?>
          <a href="#" class="btn btn-outline-primary px-5" id="printers_form_edit_save"> <i class="fas fa-save"></i>
          </a>
        <?php } ?>
      <?php endif; ?>
    </div>
  </div>
</div>