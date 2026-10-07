<form id="form_expedition_point">
  <fieldset class="border rounded p-3 mb-3">

    <legend class="w-auto px-2 fw-semibold small">
      Punto de Expedición
    </legend>

    <div class="row col-md-12 mb-3">

      <div class="col-md-12">
        <label>Facturar en</label>

        <select class="form-control" name="select_sucursal" id="select_sucursal" required>
          <?php foreach ($sucursal as $suc): ?>
            <option value="<?= esc($suc['sucursal']) ?>">
              <?= 'CENTRO DE COMPRAS ' . $suc['sucursal_name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <br>

      <div class="col-md-12">
        <input type="text" name="invoice_number" class="form-control" id="invoice_number">
      </div>

    </div>

    <input type="hidden" name="device" id="device" value="<?= esc(session()->get('device')) ?>">

    <input type="hidden" name="expedition_point_id" id="expedition_point_id">

    <input type="hidden" name="sequence_id" id="sequence_id">

    <input type="hidden" name="sequence_number" id="sequence_number">

    <input type="hidden" name="box" id="box" value="<?= esc($box) ?>">

  </fieldset>
</form>

<!-- <form id="form_expedition_point">
  <fieldset class="border rounded p-3 mb-3">

    <legend class="w-auto px-2 fw-semibold small">
      Punto de Expedición
    </legend>
    <div class="row col-md-12 mb-3">

       SELECT -->
<!-- <div class="col-md-12">
  <label>Facturar en </label>
  <select class="form-control" name="select_sucursal" id="select_sucursal" required>
    <?php //foreach ($sucursal as $suc): ?>
      <option value="<? php// esc($suc['sucursal']) ?>">
        <?php //'CENTRO DE COMPRAS ' . $suc['sucursal_name'] ?>
      </option>
    <?php // endforeach; ?>
  </select>
</div>
<br> -->
<!-- INPUT -->
<!-- <div class="col-md-12"> -->
<!-- <label>Numero </label> mantiene altura alineada -->
<!-- <input type="text" name="invoice_number" class="form-control" id="invoice_number">
</div>

</div>
<input type="text" name="user_id" id="user_id" value=" <? php// $user ?> " hidden>
<input type="text" name="expedition_point_id" id="expedition_point_id" hidden>
<input type="text" name="sequence_id" id="sequence_id" hidden>
<input type="text" name="sequence_number" id="sequence_number" hidden>
<input type="text" name="box" id="box" value=" <? php// $box ?> " hidden>




</fieldset>
</form> -->