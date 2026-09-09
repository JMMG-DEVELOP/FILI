<fieldset class="mt-4 border rounded p-3 mb-3">
  <legend class="w-auto px-2 fw-semibold small">
    Procedimientos Especiales
  </legend>
  <form id="form_cash_credit_payment">
    <fieldset class="mt-0 border rounded p-3 mb-3">
      <legend class="w-auto px-2 fw-semibold small">
        Anotar Saldo en Credito
      </legend>
      <div class="row">

        <div class="col-md-2 mb-2">
          <label> Saldo </label>
          <input type="text" class="form-control money" name="cash_credit_mount" id="cash_credit_mount"
            placeholder="Monto">
        </div>
        <div class="col-md-8 mb-2">
          <label> Cliente </label>
          <input type="text" class="form-control" name="cash_credit_customer" id="cash_credit_customer"
            placeholder="Cliente">
        </div>
        <div class="col-md-2 mb-2">
          <label> </label>
          <button type="button" class=" form-control btn btn-outline-success multi_payment_cash_credit">
            <i class="fas fa-check"></i>
          </button>
        </div>

      </div>
    </fieldset>
  </form>
  <form id="form_cash_digist_payment">
    <fieldset class="mt-0 border rounded p-3 mb-3">
      <legend class="w-auto px-2 fw-semibold small">
        Cobrar Saldo con otro metodo de pago
      </legend>
      <div class="row">


        <div class="col-md-2 mb-2">
          <label> Saldo </label>
          <input type="text" class="form-control money" name="cash_digits_mount" id="cash_digits_mount"
            placeholder="Monto">
        </div>

        <div class="col-md-2 mb-2">
          <label> Forma </label>
          <select class="form-control select" name="cash_digits_payment_type" id="cash_digits_payment_type" required>
            <option value="">Seleccione</option>
            <?php foreach ($payments as $payment): ?>
              <option value="<?= esc($payment['id']) ?>">
                <?= esc($payment['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3 mb-2">
          <label> Numero </label>
          <input type="text" class="form-control" name="cash_digits_number" id="cash_digits_number"
            placeholder="Numero de Operacion">
        </div>

        <div class="col-3 text-center">
          <small class="text-muted d-block">
            DISPOSITIVO
          </small>

          <select class="form-control select" name="cash_digits_device_payment" id="payment_device" required>
            <option value="2">SUDAMERIS</option>

            <?php foreach ($devices as $key): ?>
              <option value="<?= esc($key['id']) ?>">
                <?= esc($key['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2 mb-2">
          <label> </label>
          <button type="button" class=" form-control btn btn-outline-success multi_payment_cash_digits">
            <i class="fas fa-check"></i>
          </button>
        </div>

      </div>
    </fieldset>
  </form>

  <div class="pt-4 text-right">
    <button type="button" class="btn btn-outline-danger multi_payment_hide">
      <i class="fas fa-times"></i>
    </button>
  </div>
</fieldset>