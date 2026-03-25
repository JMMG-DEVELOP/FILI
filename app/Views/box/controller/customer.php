<form id="form_customer">
  <fieldset class="border rounded p-3 mb-3">
    <legend class="w-auto px-2 fw-semibold small">
      Datos del Cliente
    </legend>

    <div class="row">

      <!-- RUC / CI -->
      <div class="col-md-3 mb-2">
        <label>RUC / CI</label>
        <input type="text" class="form-control" name="ruc_ci" id="ruc_ci" placeholder="RUC o CI"
          value="<?= esc($result['ci']) ?>">
      </div>

      <!-- Nombre / Denominación -->
      <div class="col-md-9 mb-2">
        <label>Nombre / Denominación</label>
        <input type="text" class="form-control" name="customer_name" id="customer_name"
          placeholder="Nombre o Razón Social" value="<?= esc($result['name']) ?>">
      </div>

    </div>

    <div id="customer_search_panel"></div>
  </fieldset>
</form>