<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?= $title ?> </h5>
      <a href="#" class="btn btn-outline-danger" id="devices_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_devices">
        <input type="hidden" id="devices_id" name="devices_id" value="<?= esc($values['id'] ?? '') ?>">

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            DESCRIPCIÓN
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="name" type="text" class="form-control" value="<?= esc($values['name'] ?? '') ?>" required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            UUID
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="uuid" type="text" class="form-control" value="<?= esc($values['uuid'] ?? '') ?>" readonly required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            TIPO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="type" type="text" class="form-control" value="<?= esc($values['type'] ?? '') ?>" readonly required >
          </div>
        </div>
        <<div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            ESTADO
          </label>
       <div class="col-12 col-sm-8 col-lg-6">
    <select name="enabled" class="form-control" required>
        <?php foreach ($status as $item) { ?>
            <option
                value="<?= $item['id'] ?>"
                <?= ((int)($values['enabled'] ?? 0) === (int)$item['id']) ? 'selected' : '' ?>
            >
                <?= esc($item['name']) ?>
            </option>
        <?php } ?>
    </select>

</div>
    </div>

    </form>
  </div>
  <div class="card-footer d-flex justify-content-end align-items-center">

    <?php if (can('device_edit')): ?>
      <?php if ($type === 'edit') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="devices_form_edit_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>
    <?php endif; ?>
  </div>
</div>
</div>