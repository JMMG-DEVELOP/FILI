<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0" id="change_txt">

      </h3>

    </div>
    <div class="card-body">
      <form id="form_customer_credit">

        <input type="hidden" id="customer_id" name="id" value="<?= esc($values['id'] ?? '') ?>">

        <input type="hidden" name="customer" value="<?= esc($values['customer'] ?? '') ?>">

        <div class="form-group row">
          <label class="col-12 col-sm-3 col-form-label text-sm-right">
            CUENTA | SALDO
          </label>

          <div class="col-12 col-sm-8 col-lg-6">
            <input name="amount" type="text" class="form-control guaranies input-cash"
              value="<?= number_format((float) ($values['amount'] ?? 0), 0, ',', '.') ?>" required readonly>
          </div>
        </div>

      </form>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">

      <?php if (can('customer_credit_payment')): ?>

        <a href="#" class="btn btn-outline-primary px-5" id="credit_payment_form_open"> REALIZAR COBRO
        </a>

      <?php endif; ?>
      <?php if (can('customer_credit_add')): ?>

        <a href="#" class="btn btn-outline-primary px-5" id="credit_add_form_open"> AGREGAR A LA CUENTA
        </a>

      <?php endif; ?>

    </div>
  </div>
</div>