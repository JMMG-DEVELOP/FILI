<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-body">

      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Compras en General</h3>
      </div>
      <!--  -->
      <div class="row">

        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
          <div class="card border-3 border-top border-top-primary">
            <div class="card-body">
              <h5 class="text-muted">Ventas</h5>
              <h3 class="mb-1" id="card_total_sales">₲s. 0</h3>
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
          <div class="card border-3 border-top border-top-secondary">
            <div class="card-body">
              <h5 class="text-muted">Costo</h5>
              <h3 class="mb-1" id="card_total_cost">₲s. 0</h3>
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
          <div class="card border-3 border-top border-top-success">
            <div class="card-body">
              <h5 class="text-muted">Margen</h5>
              <h3 class="mb-1" id="card_total_margin">₲s. 0</h3>
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
          <div class="card border-3 border-top border-top-info">
            <div class="card-body">
              <h5 class="text-muted">Cantidad</h5>
              <h3 class="mb-1" id="card_total_purchases">0</h3>
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
          <div class="card border-3 border-top border-top-warning">
            <div class="card-body">
              <h5 class="text-muted">Total Credito</h5>
              <h3 class="mb-1" id="card_total_credit">₲s. 0</h3>
            </div>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-sm-12 mb-3">
          <div class="card border-3 border-top border-top-danger">
            <div class="card-body">
              <h5 class="text-muted">Cantidad Credito</h5>
              <h3 class="mb-1" id="card_total_credit_purchases">0</h3>
            </div>
          </div>
        </div>

      </div>
      <!--  -->
      <div class="table-responsive mt-3">
        <table id="table_sales" data-customer-id="<?= esc($customer_id ?? '') ?>"
          class="table table-striped table-bordered dt-responsive nowrap" width="100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>NÚMERO</th>
              <th>TIPO DE VENTA</th>
              <th>DOCUMENTO</th>
              <th>FECHA | HORA</th>
              <th>MONTO</th>
              <th>TIPO DE PAGO</th>
              <th>SUCURSAL</th>
              <th>MARGEN</th>
              <th>USUARIO</th>
              <th>ACCIONES</th>
            </tr>
          </thead>

          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>