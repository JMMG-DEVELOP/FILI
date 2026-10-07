<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">

  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

      <h5 class="mb-0">
        <?= esc($title) ?>
      </h5>

      <a href="#" class="btn btn-outline-danger" id="sequence_form_close">

        <i class="fas fa-times"></i>

      </a>

    </div>

    <div class="card-body">

      <form id="form_sequence">

        <input type="hidden" id="sequence_id" name="id" value="<?= esc($values['id'] ?? '') ?>">

        <input type="hidden" id="sequence_type" name="type" value="<?= esc($type ?? '') ?>">

        <div class="form-group row">

          <label class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-form-label">

            CODIGO

          </label>

          <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">

            <input name="expedition_point_code" type="text" class="form-control"
              value="<?= esc($values['expedition_point_code'] ?? '') ?>" readonly>

          </div>

        </div>

        <div class="form-group row">

          <label class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-form-label">

            SUCURSAL

          </label>

          <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">

            <input name="sucursal_name" type="text" class="form-control"
              value="<?= esc($values['sucursal_name'] ?? '') ?>" readonly>

          </div>

        </div>

        <div class="form-group row">

          <label class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-form-label">

            NUMERO ACTUAL

          </label>

          <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">

            <input name="last_number" type="text" class="form-control number"
              value="<?= esc($values['last_number'] ?? '') ?>" required>

          </div>

        </div>

      </form>

    </div>

    <div class="card-footer d-flex justify-content-end">

      <button type="button" class="btn btn-outline-primary px-5" id="sequence_form_edit_save">

        <i class="fas fa-save"></i>

      </button>

    </div>

  </div>

</div>