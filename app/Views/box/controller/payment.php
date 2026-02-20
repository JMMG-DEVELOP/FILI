<fieldset class="border rounded p-3 mb-3">

  <legend class="w-auto px-2 fw-semibold small">
    Datos de Venta
  </legend>

  <div class="row">

    <!-- Tipo de Venta -->
    <div class="col-md-6 mb-2">
      <label>Tipo de <strong>V</strong>enta</label>
      <select class="form-control select" name="sales" id="sales" required>
        <?php foreach ($sales as $sls): ?>
          <option value="<?= esc($sls['id']) ?>">
            <?= esc($sls['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Tipo de Pago -->
    <div class="col-md-6 mb-2">
      <label>Tipo de <strong>P</strong>ago</label>
      <select class="form-control select" name="payment" id="payment" required>
        <?php foreach ($payment as $pyt): ?>
          <option value="<?= esc($pyt['id']) ?>">
            <?= esc($pyt['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <input type="text" name="card_percent" id="card_percent" value="<?= esc($percent) ?>" hidden>

  </div>

</fieldset>