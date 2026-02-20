<fieldset class="border rounded p-3 mb-3">

  <legend class="w-auto px-2 fw-semibold small">
    Datos Facturación
  </legend>
  <div class="row col-md-12 mb-3">

    <!-- SELECT -->
    <div class="col-md-8">
      <label>Facturar en </label>
      <select class="form-control select" name="section" id="sections" required>
        <?php foreach ($sucursal as $suc): ?>
          <option value="<?= esc($suc['sucursal']) ?>">
            <?= 'CENTRO DE COMPRAS ' . $suc['sucursal_name'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- INPUT -->
    <div class="col-md-4">
      <label>Numero Interno</label> <!-- mantiene altura alineada -->
      <input type="text" class="form-control">
    </div>

  </div>
</fieldset>