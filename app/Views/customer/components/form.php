<form id="customer_form">
  <fieldset class="border rounded p-3 mb-3 bg-light">

    <legend class="w-auto px-2 fw-semibold small">
      Nuevo del Cliente
    </legend>

    <div class="row">

      <!-- RUC / CI -->
      <div class="col-md-4 mb-2">
        <label>RUC / CI</label>
        <input type="text" class="form-control" name="ci" id="ci" placeholder="C.I. o RUC" required>
      </div>

      <!-- Nombre / Denominación -->
      <div class="col-md-8 mb-2">
        <label>Nombre / Denominación</label>
        <input type="text" class="form-control" name="name" id="name" placeholder="Nombre o razón social" required>
      </div>

      <!-- Celular -->
      <div class="col-md-4 mb-2">
        <label>Celular</label>
        <input type="text" class="form-control" name="cel" id="cel" placeholder="Celular" required>
      </div>

      <!-- Correo-->
      <div class="col-md-8 mb-2">
        <label>Correo</label>
        <input type="text" class="form-control" name="correo" id="correo" required placeholder="Correo Electronico">
      </div>

    </div>
    <div class="border-top pt-4 text-right">
      <button type="submit" class="btn btn-primary customer_send">
        <i class="fas fa-check"></i>
      </button>
      <button type="button" class="btn btn-outline-danger customer_add_cancel">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </fieldset>
</form>