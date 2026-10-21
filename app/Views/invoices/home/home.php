<div class="card">
  <div class="card-header">
    <h5 class="mb-0">ADMINISTRACIÓN DE FACTURAS</h5>
    <?= $this->include('invoices/home/card'); ?>
  </div>

  <div class="card-body">
    <?php if (can('invoice_create')): ?>
      <a href="#" class="btn btn-outline-primary" id="invoice_form_create_open">
        <i class="fas fa-plus"> CREAR FACTURA</i>
      </a>
    <?php endif; ?>
    <div class="table-responsive">
      <table id="table_invoices" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>NÚMERO</th>
            <th>FECHA</th>
            <th>HORA</th>
            <th>CLIENTE</th>
            <th>RUC</th>
            <th>MONTO</th>
            <th>USUARIO</th>
            <th>ESTATUS</th>
            <th>ANULAR</th>
          </tr>
        </thead>

        <tbody></tbody>
      </table>
    </div>
  </div>
</div>