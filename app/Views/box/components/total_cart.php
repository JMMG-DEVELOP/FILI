<div class="row g-3">

  <!-- TOTAL -->
  <div class="col-lg-10 col-md-7 col-12">
    <div class="card shadow-sm border-0 rounded-3 h-100">
      <div class="card-body">

        <div class="row g-3">

          <!-- 1. TOTAL -->
          <div class="col-md-4 col-6">
            <div class="h-100 d-flex flex-column justify-content-center">
              <small class="text-uppercase text-muted fw-semibold">
                Total
              </small>
              <h1 class="fw-bold mb-0 moneyText text-dark display-3" id="grand_total">
                0
              </h1>
            </div>
          </div>
          <div id="display_escape" class="display_escape">
            <!-- 2. ENTREGA -->
            <div class="col-md-3 col-6">
              <div class="h-100 d-flex flex-column justify-content-center">
                <small class="text-uppercase text-muted fw-semibold">
                  Entrega
                </small>
                <input id="cash_payment" class="form-control form-control-lg text-center fw-bold  input-cash money">
              </div>
            </div>

            <!-- 3. VUELTO -->
            <div class="col-md-3 col-6">
              <div class="h-100 d-flex flex-column justify-content-center">
                <small class="text-uppercase text-muted fw-semibold">
                  Vuelto
                </small>
                <h1 class="fw-bold mb-0 money text-dark display-5" id="change">
                  VUELTO
                </h1>
              </div>
            </div>

            <!-- 4. COMPROBANTE -->
            <div class="col-md-2 col-6">
              <div class="h-100 d-flex flex-column justify-content-center">
                <small class="text-uppercase text-muted fw-semibold">
                  Comprobante
                </small>
                <select class="form-control text-center fw-bold" id="receipt_type">
                  <option value="1">Ticket</option>
                  <option value="2">Factura</option>
                </select>
              </div>
            </div>
          </div>

          <div id="display_other_pay" class="display_escape">
            <div class="col-md-3 col-6">
              <div class="h-100 d-flex flex-column justify-content-center">
                <small class="text-uppercase text-muted fw-semibold">

                </small>
                <input type="button" id="confirm_payment" class="btn btn-outline-brand" value="CONFIRMAR">
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