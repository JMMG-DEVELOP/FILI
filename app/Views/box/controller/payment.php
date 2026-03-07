<fieldset class="border rounded p-3 mb-3">

  <legend class="w-auto px-2 fw-semibold small">
    Datos de Venta
  </legend>

  <div class="row">

    <!-- COLUMNA IZQUIERDA -->
    <div class="col-md-6">

      <div class="mb-2">
        <label>Tipo de <strong>V</strong>enta</label>
        <select class="form-control select" name="sales" id="sales" required>
          <?php foreach ($sales as $sls): ?>
            <option value="<?= esc($sls['id']) ?>">
              <?= esc($sls['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-2">
        <select class="form-control select" name="sales_percent" id="sales_percent">
          <?php foreach ($percent as $p): ?>
            <option value="<?= esc($p['cant']) ?>">
              <?= esc($p['cant']) ?> %
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

    <!-- COLUMNA DERECHA -->
    <div class="col-md-6">

      <div class="mb-2">
        <label>Tipo de <strong>P</strong>ago</label>
        <select class="form-control select" name="payment" id="payment" required>
          <?php foreach ($payment as $pyt): ?>
            <option value="<?= esc($pyt['id']) ?>">
              <?= esc($pyt['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-2">
        <select class="form-control select" name="payment_percent" id="payment_percent">
          <option value="5">5</option>
          <?php foreach ($percent as $p): ?>
            <option value="<?= esc($p['cant']) ?>">
              <?= esc($p['cant']) ?> %
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

  </div>

</fieldset>