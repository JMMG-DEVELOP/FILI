<form id="form_movement">
  <fieldset class="border rounded p-3 mb-3">
    <legend class="w-auto px-2 fw-semibold small">
      Movimiento de Efectivo
    </legend>

    <div class="row">

      <!-- Monto -->
      <div class="col-md-3 mb-2">
        <label>Monto</label>
        <input type="text" class="form-control money" name="mount" id="mount" placeholder="Monto">
      </div>

      <!-- Tipo de Movimiento -->
      <div class="col-md-6 mb-2">
        <label>Tipo de Movimiento</label>
        <select class="form-control select" name="type_movement" id="type_movement">
          <option value=""> Seleccione </option>
          <?php foreach ($type as $p): ?>
            <option value="<?= esc($p['id']) ?>">
              <?= esc($p['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3 mb-2">
        <label> </label>
        <button type="button" class=" form-control btn btn-outline-success box_movement_send">
          <i class="fas fa-check"></i>
        </button>
      </div>

    </div>
    <div class="border-top pt-4 text-right">
      <button type="button" class="btn btn-outline-danger box_movement_hide">
        <i class="fas fa-times"></i>
      </button>
    </div>

  </fieldset>
</form>