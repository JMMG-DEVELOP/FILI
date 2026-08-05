<div class="row">

  <div class="col-md-6 col-6">
    <div class="h-100 d-flex flex-column justify-content-center">

      <small class="text-uppercase text-muted fw-semibold">
        Comprobante
      </small>

      <select class="form-control text-center fw-bold" id="receipt_type">

        <?php foreach ($types as $type): ?>

          <option value="<?= esc($type['id']) ?>">
            <?= esc($type['name']) ?>
          </option>

        <?php endforeach; ?>

      </select>

    </div>
  </div>

  <div class="col-md-6 col-6">
    <div class="h-100 d-flex flex-column justify-content-center">

      <small class="text-uppercase text-muted fw-semibold">
        Imprimir
      </small>

      <select class="form-control text-center fw-bold" id="print_type">
        <option value="1">TICKET</option>
        <option value="2">FACTURA</option>
        <option value="0">NINGUNO</option>
      </select>

    </div>
  </div>

</div>