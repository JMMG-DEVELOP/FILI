<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?= $title ?> </h5>
      <a href="#" class="btn btn-outline-danger" id="provider_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_provider">
        <input type="hidden" id="provider_id" name="id" value="<?= esc($values['id'] ?? '') ?>">
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            NOMBRE
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="name" type="text" class="form-control" value="<?= esc($values['name'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            RUC
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="ruc" type="text" class="form-control ruc" value="<?= esc($values['ruc'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            TIMBRADO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="timbrado" type="text" class="form-control ruc" value="<?= esc($values['timbrado'] ?? '') ?>"
              required>
          </div>
        </div>
      </form>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">
      <?php if ($type === 'new') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="provider_form_new_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>
      <?php if (can('provider_edit')): ?>
        <?php if ($type === 'edit') { ?>
          <a href="#" class="btn btn-outline-primary px-5" id="provider_form_edit_save"> <i class="fas fa-save"></i>
          </a>
        <?php } ?>
      <?php endif; ?>
    </div>
  </div>
</div>