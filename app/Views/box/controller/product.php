<div class="row mt-4">
  <!-- Descripción -->
  <div class="col-md-12 mb-2">
    <label>Descripción</label>
    <input type="text" class="form-control" name="description" id="description" value=" <?= $result['description'] ?>">
  </div>

  <div class="col-md-4 mb-2">
    <label>Precio 1</label>
    <input type="text" class="form-control money" name="price_one" id="price_one"
      value=" <?= $result['prices']['price_one'] ?> ">
  </div>

  <!-- Precio 2 -->
  <div class="col-md-3 mb-2">
    <label>Precio 2</label>
    <input type="text" class="form-control money" name="precio_two" id="price_two"
      value=" <?= $result['prices']['price_two'] ?>">
  </div>
  <!-- Cant -->
  <div class="col-md-2 mb-2">
    <label>Cant </label>
    <input type="text" class="form-control" name="cant_price_two" id="cant_price_two"
      value=" <?= $result['cant_two'] ?>">
  </div>
  <!-- Precio Card -->
  <div class="col-md-3 mb-2">
    <label>Precio Card</label>
    <input type="text" class="form-control money" name="precio_card" id="price_card"
      value=" <?= $result['prices']['price_card'] ?>">
  </div>
</div>

<div class="border-top pt-4 text-right">
  <button type="button" class="btn btn-primary product_send_cart">
    <i class="fas fa-check"></i>
  </button>
  <button type="button" class="btn btn-outline-danger product_form_cancel">
    <i class="fas fa-times"></i>
  </button>
</div>