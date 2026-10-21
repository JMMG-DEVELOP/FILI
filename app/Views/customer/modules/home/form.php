<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0"><?= $title ?> </h3>
      <a href="#" class="btn btn-outline-danger" id="customer_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_customer">
        <input type="hidden" id="customer_id" name="id" value="<?= esc($values['id'] ?? '') ?>">
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            NOMBRE Y APELLIDO | DENOMINACIÓN
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="name" type="text" class="form-control" value="<?= esc($values['name'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CEDULA | RUC
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="ci" type="text" class="form-control ruc" value="<?= esc($values['ci'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CORREO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="correo" type="email" class="form-control" value="<?= esc($values['correo'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CELULAR
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="cel" type="text" class="form-control number" value="<?= esc($values['cel'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CREDITO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <select name="credit_status" id="credit_status" class="form-control" required>
              <option value="1" <?= (string) ($values['credit_status'] ?? '') === '1' ? 'selected' : '' ?>>
                Habilitado
              </option>

              <option value="2" <?= (string) ($values['credit_status'] ?? '') === '2' ? 'selected' : '' ?>>
                Deshabilitado
              </option>
            </select>
          </div>
        </div>

      </form>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">
      <?php if ($type === 'new') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="customer_form_new_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>
      <?php if (can('customer_edit')): ?>
        <?php if ($type === 'edit') { ?>
          <a href="#" class="btn btn-outline-primary px-5" id="customer_form_edit_save"> <i class="fas fa-save"></i>
          </a>
        <?php } ?>
      <?php endif; ?>
    </div>
  </div>
</div>