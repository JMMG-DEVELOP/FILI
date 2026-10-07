<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">

  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

      <h5 class="mb-0">
        <?= esc($title) ?>
      </h5>

      <a href="#" class="btn btn-outline-danger" id="point_point_form_close">
        <i class="fas fa-times"></i>
      </a>

    </div>

    <div class="card-body">

      <form id="form_point">

        <!-- ID -->
        <input type="hidden" id="point_id" name="id" value="<?= esc($values['id'] ?? '') ?>">


        <!-- CODIGO -->
        <div class="form-group row">

          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CODIGO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">

            <input name="code" type="text" class="form-control number" value="<?= esc($values['code'] ?? '') ?>"
              required>

          </div>

        </div>


        <!-- SUCURSAL -->
        <div class="form-group row">

          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            SUCURSAL
          </label>

          <div class="col-12 col-sm-8 col-lg-6">

            <select id="sucursal" name="sucursal" class="form-control" required>

              <option value="">
                Seleccione
              </option>

              <?php foreach ($sucursals as $item): ?>

                <option value="<?= esc($item['id']) ?>" <?= (($values['sucursal'] ?? '') == $item['id']) ? 'selected' : '' ?>
                  >
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

        <a href="#" class="btn btn-outline-primary px-5" id="point_point_form_new_save">
          <i class="fas fa-save"></i>
        </a>

      <?php endif; ?>


      <?php if (can('point_point_edit')): ?>

        <?php if ($type === 'edit'): ?>

          <a href="#" class="btn btn-outline-primary px-5" id="point_point_form_edit_save">
            <i class="fas fa-save"></i>
          </a>

        <?php endif; ?>

      <?php endif; ?>

    </div>

  </div>

</div>