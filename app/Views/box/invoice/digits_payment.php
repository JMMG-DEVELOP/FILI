<form id="form_digist_payment">
  <div class="row g-3">

    <!-- TOTAL -->
    <div class="col-lg-10 col-md-7 col-12">
      <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">

          <div class="row">

            <div class="col-12">

              <!-- TOTAL -->
              <small class="text-uppercase text-muted fw-semibold">
                Total Gs.
              </small>

              <h1 class="fw-bold mb-2 moneyText text-dark display-3" id="cash_grand_total">
                0
              </h1>

              <!-- DATOS -->
              <div class="row w-100 g-3">

                <!-- NUMERO DE OPERACION -->
                <div class="col-6 text-center">
                  <small class="text-muted d-block">
                    NUMERO DE OPERACIÓN
                  </small>

                  <input name="payment_device_operation_number" id="payment_device_operation_number"
                    class="form-control form-control-lg text-center fw-bold">
                </div>

                <!-- DISPOSITIVO -->
                <div class="col-6 text-center">
                  <small class="text-muted d-block">
                    DISPOSITIVO
                  </small>

                  <select class="form-control select" name="device_payment" id="payment_device" required>
                    <option value="2">SUDAMERIS</option>

                    <?php foreach ($devices as $key): ?>
                      <option value="<?= esc($key['id']) ?>">
                        <?= esc($key['name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ITEMS -->
    <div class="col-lg-2 col-md-5 col-12">
      <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body d-flex flex-column justify-content-center text-center">
          <small class="text-uppercase text-muted fw-semibold">

          </small>
          <h2 class="fw-bold mb-0 text-primary" id="cart_item">
            0
          </h2>
        </div>
      </div>
    </div>

  </div>
</form>