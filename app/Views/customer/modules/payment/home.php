<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0">Historial de Pagos del Cliente</h3>
    </div>

    <div class="card-body">
      <div class="table-responsive">

        <table id="table_payment" data-customer-id="<?= esc($customer) ?>"
          class="table table-striped table-bordered dt-responsive nowrap" width="100%">

          <thead>
            <tr>
              <th>ID</th>
              <th>FECHA</th>
              <th>HORA</th>
              <th>MONTO</th>
              <th>DETALLE</th>
              <th>USUARIO</th>
            </tr>
          </thead>

          <tbody></tbody>

        </table>

      </div>
    </div>

  </div>
</div>