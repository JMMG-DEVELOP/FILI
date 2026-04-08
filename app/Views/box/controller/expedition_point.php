<form id="form_expedition_point">
  <fieldset class="border rounded p-3 mb-3">

    <legend class="w-auto px-2 fw-semibold small">
      Punto de Expedición
    </legend>
    <div class="row col-md-12 mb-3">

      <!-- SELECT -->
      <div class="col-md-8">
        <label>Facturar en </label>
        <select class="form-control" name="select_sucursal" id="select_sucursal" required>
          <?php foreach ($sucursal as $suc): ?>
            <option value="<?= esc($suc['sucursal']) ?>">
              <?= 'CENTRO DE COMPRAS ' . $suc['sucursal_name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- INPUT -->
      <div class="col-md-4">
        <label>Numero </label> <!-- mantiene altura alineada -->
        <input type="text" name="invoice_number" class="form-control" id="invoice_number">
      </div>

    </div>
    <input type="text" name="user_id" id="user_id" value=" <?= $user ?> " hidden>
    <input type="text" name="expedition_point_id" id="expedition_point_id" hidden>
    <input type="text" name="sequence_id" id="sequence_id" hidden>
    <input type="text" name="session_id" id="session_id" value=" <?= $session ?>" hidden>


  </fieldset>
</form>