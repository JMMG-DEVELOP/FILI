<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <?php if ($type === 'payment') { ?>
        <h5 class="mb-0"> Registrar Pago </h5>
      <?php } ?>
      <?php if ($type === 'add') { ?>
        <h5 class="mb-0"> Agregar a la cuenta </h5>
      <?php } ?>
      <a href="#" class="btn btn-outline-danger" id="customer_view_form_close"> <i class="fas fa-times"></i>
      </a>
    </div>
    <div class="card-body">
      <form id="form_customer_save">

        <input type="hidden" name="id" value="<?= esc($values['id'] ?? '') ?>">

        <input type="hidden" name="customer" value="<?= esc($values['customer'] ?? '') ?>">

        <input name="mount" type="hidden" value="<?= number_format((float) ($values['amount'] ?? 0), 0, ',', '.') ?>">

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            MONTO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <input name="amount" id="amount" type="text" class="form-control input-cash guaranies" value="" required>
          </div>
        </div>

        <?php if ($type === 'payment'): ?>
          <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label text-sm-right">
              DETALLE
            </label>

            <div class="col-12 col-sm-8 col-lg-6">
              <input name="detail" type="text" class="form-control" value="" required>
            </div>
          </div>
        <?php endif; ?>

      </form>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">
      <?php if ($type === 'payment') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="credit_payment_form_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>

      <?php if ($type === 'add') { ?>
        <a href="#" class="btn btn-outline-primary px-5" id="credit_add_form_save"> <i class="fas fa-save"></i>
        </a>
      <?php } ?>

    </div>
  </div>
</div>