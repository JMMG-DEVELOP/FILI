<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><?= $title ?> </h5>
      <a href="#" class="btn btn-outline-danger" id="users_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_users">
        <input type="hidden" id="users_id" name="id" value="<?= esc($values['id'] ?? '') ?>">
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            NOMBRE Y APELLIDO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="name" type="text" class="form-control" value="<?= esc($values['name'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            USUARIO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="user" type="text" class="form-control" value="<?= esc($values['user'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CEDULA
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="ci" type="text" class="form-control number" value="<?= esc($values['ci'] ?? '') ?>" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CELULAR
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <input name="phone" type="text" class="form-control number" value="<?= esc($values['phone'] ?? '') ?>"
              required>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CATEGORIA
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <select name="category" class="form-control" required>
              <option value="">
                Seleccione
              </option>
              <?php foreach ($category as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['category'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            ESTADO
          </label>
          <div class="col-12 col-sm-8 col-lg-6">
            <select name="status" class="form-control" required>
              <option value="">
                Seleccione
              </option>
              <?php foreach ($status as $item) { ?>
                <option value="<?= $item['id'] ?>" <?= (($values['status'] ?? '') == $item['id']) ? 'selected' : '' ?>>
                  <?= esc($item['name']) ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <?php if ($type === 'new') { ?>
          <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label text-sm-right">
              CONTRASEÑA
            </label>

            <div class="col-12 col-sm-8 col-lg-6">
              <div class="input-group">

                <input name="password" id="password" type="password" class="form-control" required>

                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary" id="toggle_password" title="Mostrar contraseña">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>

              </div>
            </div>
          </div>
        <?php } ?>


      </form>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">
      <?php if ($type === 'new') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="users_form_new_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>
      <?php if (can('user_edit')): ?>
        <?php if ($type === 'edit') { ?>
          <a href="#" class="btn btn-outline-primary px-5" id="users_form_edit_save"> <i class="fas fa-save"></i>
          </a>
        <?php } ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php if (can('user_edit')): ?>
  <div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Editar Contraseña </h5>
      </div>
      <div class="card-body">
        <form id="form_password">
          <input type="hidden" id="users_id" name="id" value="<?= esc($values['id'] ?? '') ?>">
          <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label text-sm-right">
              Nueva Contraseña
            </label>
            <div class="col-12 col-sm-8 col-lg-6">
              <div class="input-group">
                <input name="password" id="password" type="password" class="form-control" required>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-secondary" id="toggle_password" title="Mostrar contraseña">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="card-footer d-flex justify-content-end align-items-center">
        <?php if ($type === 'edit') { ?>
          <a href="#" class="btn btn-outline-primary px-5" id="users_form_edit_password_save"> <i class="fas fa-save"></i>
          </a>
        <?php } ?>
      </div>
    </div>
  </div>
<?php endif; ?>