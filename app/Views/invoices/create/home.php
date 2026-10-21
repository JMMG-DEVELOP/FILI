<div class="row">

  <!-- LISTA DE VENTAS -->
  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        Lista de Ventas
      </div>

      <div class="card-body">
        <?= $this->include('invoices/create/list'); ?>
      </div>
    </div>
  </div>

  <!-- NUEVA FACTURA -->
  <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Nueva Factura</h3>
        <!-- BOTÓN MOSTRAR / OCULTAR -->
        <a href="#" class="btn btn-outline-danger px-5" id="invoice_form_create_close"> <i class="fas fa-times"></i>
        </a>
      </div>
      <div class="card-header d-flex justify-content-between align-items-center">

        <div class="form-group">
          <label for="inputText3" class="col-form-label">Total</label>
          <input id="total_invoice" type="text" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label for="inputText3" class="col-form-label">Items</label>
          <input id="items_invoice" type="text" class="form-control" readonly>
        </div>

        <button type="button" id="toggle_window" class="btn btn-outline-primary mb-3">
          <i class="fa fa-eye"></i></button>

      </div>

      <div class="card-body">
        <?= $this->include('invoices/create/new'); ?>
      </div>
    </div>
  </div>

</div>