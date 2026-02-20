<div class="row justify-content-center">
  <div class="col-xl-12 col-lg-12 col-md-12">

    <div class="card shadow-sm">

      <!-- ==================== HEADER ==================== -->
      <div class="card-header bg-light text-center">
        <div class="border-top pt-2 text-right">
          <button type="button" class="btn btn-outline-danger product_cancel">
            X
          </button>
        </div>
        <h3 class="mb-0 font-weight-bold">
          <?= esc($title) ?>
        </h3>
      </div>

      <!-- ==================== BODY ==================== -->
      <div class="card-body">
        <form id="product_form">
          <?= csrf_field() ?>

          <div class="row">
            <input type="text" name="operation_type" id="operation_type" hidden>
            <input type="text" name="card_percent" id="card_percent_cant" value=" <?= $card_percent ?>" hidden>


            <!-- ==================== DESCRIPCIÓN ==================== -->
            <div class="col-md-12">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">Descripción</legend>

                <!-- Codigo -->
                <div class="form-row">
                  <div class="col-md-2 mb-2">
                    <label>Código</label>
                    <input type="text" class="form-control" name="code" id="code" required>
                  </div>

                  <!-- Descripción -->
                  <div class="col-md-4 mb-2">
                    <label>Descripción</label>
                    <input type="text" class="form-control" name="description" id="description" require autofocus>
                  </div>

                  <!-- Sección -->
                  <div class="col-md-2 mb-2">
                    <label>Sección</label>
                    <select class="form-control select" name="section" id="sections" required>
                      <option value="">Seleccione</option>
                      <?php foreach ($sections as $section): ?>
                        <option value="<?= esc($section['id']) ?>">
                          <?= esc($section['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Marca -->
                  <div class="col-md-2 mb-2">
                    <label>Marca</label>
                    <select class="form-control select" name="brand" id="brands" required>
                      <option value="">Seleccione</option>
                      <?php foreach ($brands as $brand): ?>
                        <option value="<?= esc($brand['id']) ?>">
                          <?= esc($brand['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <!-- Forma de Venta -->
                  <div class="col-md-2 mb-2">
                    <label>Forma de venta</label>
                    <select class="form-control select" name="sales" id="sales" required>
                      <option value="">Seleccione</option>
                      <?php foreach ($sales as $sale): ?>
                        <option value="<?= esc($sale['id']) ?>">
                          <?= esc($sale['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </fieldset>
            </div>

            <!-- ==================== FORMA DE VENTA ==================== -->


          </div>

          <div class="row">

            <!-- ==================== STOCK ==================== -->
            <?php if (can('product_stock_view')): ?>
              <div class="col-md-2">
                <fieldset class="border p-3 mb-3 h-100">
                  <legend class="w-auto px-2 font-weight-bold">Stock</legend>

                  <!-- FILA 1 -->
                  <div class="form-row align-items-end mb-2">

                    <!-- Stock Fili -->
                    <div class="col">
                      <label>Stock Fili</label>
                      <input type="text" class="form-control" name="stock_1" id="stock_1" readonly>
                    </div>

                    <?php if (can('product_stock_edit')): ?>
                      <!-- Edición Fili -->
                      <div class="col">
                        <label></label>
                        <input type="text" class="form-control stock" name="edit_stock_1" id="edit_stock_1" required>
                      </div>
                    <?php endif; ?>

                  </div>

                  <!-- FILA 2 -->
                  <div class="form-row align-items-end">

                    <!-- Stock Fili 2 -->
                    <div class="col">
                      <label>Stock Fili 2</label>
                      <input type="text" class="form-control" name="stock_2" id="stock_2" readonly>
                    </div>

                    <?php if (can('product_stock_edit')): ?>
                      <!-- Edición Fili 2 -->
                      <div class="col">
                        <label></label>
                        <input type="text" class="form-control stock" name="edit_stock_2" id="edit_stock_2" required>
                      </div>
                    <?php endif; ?>

                  </div>

                </fieldset>
              </div>

            <?php endif; ?>
            <!-- ==================== COSTOS ==================== -->
            <?php if (can('product_cost_view')): ?>
              <div class="col-md-2">
                <fieldset class="border p-3 mb-3 h-100">
                  <legend class="w-auto px-2 font-weight-bold">Costo</legend>

                  <!-- Costo -->
                  <label>Costo</label>
                  <input type="text" class="form-control money mb-2" name="cost" id="cost" required>

                  <!-- Último Costo -->
                  <label>Último costo</label>
                  <input type="text" class="form-control money" name="other_cost" id="other_cost">

                </fieldset>
              </div>
            <?php endif; ?>

            <!-- ==================== VENTAS ==================== -->
            <div class="col-md-6">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">Venta</legend>

                <!-- Precio 1 -->
                <div class="form-row">
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio 1</strong></label>
                    <input type="text" class="form-control money" name="price_one" id="price_one" required>
                  </div>

                  <!-- Margen 1 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_one" required>
                    </div>

                    <!-- Porcentaje 1 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x1" id="x1" required>
                    </div>
                  <?php endif; ?>


                </div>

                <!-- Precio 2 -->
                <div class="form-row">
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio 2</strong></label>
                    <input type="text" class="form-control money" name="price_two" id="price_two" required>
                  </div>

                  <!-- Margen 2 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_two" required>
                    </div>

                    <!-- Porcentaje 2 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x2" required>
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>Cant</label>
                    <input type="text" class="form-control numeric" name="cant_two" id="cant_two" required>
                  </div>
                </div>

                <!-- Precio 3 -->
                <div class="form-row">

                  <!-- Precio -->
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio Card</strong></label>
                    <!-- <input type="text" class="form-control money" name="price_three" id="price_three" hidden> -->
                    <input type="text" class="form-control money" name="price_card" id="price_card" required>

                  </div>

                  <!-- Margen 3 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-3 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_card" id="margin_card" required>
                    </div>

                    <div class="col-md-3 mb-2">
                      <label><strong>Descuento</strong></label>
                      <input type="text" class="form-control money" name="discount_card" id="discount_card" required>
                    </div>

                  <?php endif; ?>

                </div>

              </fieldset>
            </div>

            <!-- ==================== IVA ==================== -->
            <div class="col-md-2">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">Iva</legend>
                <label>%</label>
                <select class="form-control select" name="iva" id="iva" required>
                  <option value="1">10 %</option>
                  <?php foreach ($ivas as $iva): ?>
                    <option value="<?= esc($iva['id']) ?>">
                      <?= esc($iva['name']) . ' %'; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </fieldset>
            </div>

          </div>

          <!-- ==================== FOOTER ==================== -->
          <div class="border-top pt-3 text-right">
            <?php if (can('product_product_edit')): ?>
              <button type="submit" class="btn btn-primary product_send">
                <i class="fas fa-check"></i>
              </button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-danger product_cancel">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>