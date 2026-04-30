<fieldset class="mt-4 border rounded p-3 mb-3">
  <legend class="w-auto px-2 fw-semibold small">
    Procedimientos Especiales
  </legend>
  <form id="procedure_credit_payment">
    <fieldset class="mt-0 border rounded p-3 mb-3">
      <legend class="w-auto px-2 fw-semibold small">
        Anotar Saldo en Credito
      </legend>
      <div class="row">

        <div class="col-md-3 mb-2">
          <label> Saldo </label>
          <input type="text" class="form-control money" name="value_credit_mount" id="value_credit_mount"
            placeholder="Monto">
        </div>
        <div class="col-md-6 mb-2">
          <label> Cliente </label>
          <input type="text" class="form-control" name="value_credit_customer" id="value_credit_customer"
            placeholder="Cliente">
        </div>
        <div class="col-md-3 mb-2">
          <label> </label>
          <button type="button" class=" form-control btn btn-outline-success procedures_credit_send">
            <i class="fas fa-check"></i>
          </button>
        </div>

      </div>
    </fieldset>
  </form>
  <form id="procedure_other_payment">
    <fieldset class="mt-0 border rounded p-3 mb-3">
      <legend class="w-auto px-2 fw-semibold small">
        Cobrar Saldo con otro metodo de pago
      </legend>
      <div class="row">


        <div class="col-md-3 mb-2">
          <label> Saldo </label>
          <input type="text" class="form-control money" name="value_payment_mount" id="value_payment_mount"
            placeholder="Monto">
        </div>
        <div class="col-md-6 mb-2">
          <label> Metodo de Pago </label>
          <select class="form-control select" name="value_payment_type" id="value_payment_type" required>
            <option value="">Seleccione</option>
            <?php foreach ($payments as $payment): ?>
              <option value="<?= esc($payment['id']) ?>">
                <?= esc($payment['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3 mb-2">
          <label> </label>
          <button type="button" class=" form-control btn btn-outline-success procedures_payment_send">
            <i class="fas fa-check"></i>
          </button>
        </div>

      </div>
    </fieldset>
  </form>

  <div class="pt-4 text-right">
    <button type="button" class="btn btn-outline-danger procedures_hide">
      <i class="fas fa-times"></i>
    </button>
  </div>
</fieldset>