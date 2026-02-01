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

            <!-- ==================== DESCRIPCIÓN ==================== -->
            <div class="col-md-10">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">Descripción</legend>

                <!-- Codigo -->
                <div class="form-row">
                  <div class="col-md-3 mb-2">
                    <label>Código</label>
                    <input type="text" class="form-control" name="code" id="code" required>
                  </div>

                  <!-- Descripción -->
                  <div class="col-md-5 mb-2">
                    <label>Descripción</label>
                    <input type="text" class="form-control" name="description" id="description" require>
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
                </div>
              </fieldset>
            </div>

            <!-- ==================== FORMA DE VENTA ==================== -->
            <div class="col-md-2">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold"></legend>

                <!-- Unidad -->
                <div class="custom-control custom-radio mb-2">
                  <input type="radio" class="custom-control-input" id="sales_1" name="tipo_venta" value="1" checked
                    required>
                  <label class="custom-control-label" for="sales_1">
                    <strong>Uni</strong>
                  </label>
                </div>

                <!-- Kilogramo -->
                <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input" id="sales_2" name="tipo_venta" value="2" required>
                  <label class="custom-control-label" for="sales_2">
                    <strong>Kg</strong>
                  </label>
                </div>
              </fieldset>
            </div>

          </div>

          <div class="row">

            <!-- ==================== STOCK ==================== -->
            <?php if (can('product_stock_view')): ?>
              <div class="col-md-2">
                <fieldset class="border p-3 mb-3 h-100">
                  <legend class="w-auto px-2 font-weight-bold">Stock</legend>

                  <!-- Stock Fili Central -->
                  <label>Stock Fili</label>
                  <input type="text" class="form-control mb-2" name="stock_1" id="stock_1" readonly>
                  <?php if (can('product_stock_edit')): ?>
                    <label>Edición</label>
                    <input type="text" class="form-control stock mb-2" name="edit_stock_1" required>
                  <?php endif; ?>
                  <!-- Stock Fili 2 -->
                  <label>Stock Fili 2</label>
                  <input type="text" class="form-control mb-2" name="stock_2" id="stock_2" readonly>
                  <?php if (can('product_stock_edit')): ?>
                    <label>Edición</label>
                    <input type="text" class="form-control stock" name="edit_stock_2" required>
                  <?php endif; ?>
                </fieldset>
              </div>
            <?php endif; ?>
            <!-- ==================== COSTOS ==================== -->
            <?php if (can('product_cost_view')): ?>
              <div class="col-md-2">
                <fieldset class="border p-3 mb-3 h-100">
                  <legend class="w-auto px-2 font-weight-bold">Costos</legend>

                  <!-- Costo -->
                  <label>Costo</label>
                  <input type="text" class="form-control money mb-2" name="cost" id="cost" required value="5000">

                  <!-- Último Costo -->
                  <label>Último costo</label>
                  <input type="text" class="form-control money" name="other_cost" id="other_cost" value="5000">

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
                    <input type="text" class="form-control money" name="price_one" required value="8000">
                  </div>

                  <!-- Margen 1 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_one" required value="3000">
                    </div>

                    <!-- Porcentaje 1 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x1" required value="20">
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>x</label>
                    <input type="text" class="form-control numeric" name="cant_one" readonly value="1">
                  </div>
                </div>

                <!-- Precio 2 -->
                <div class="form-row">
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio 2</strong></label>
                    <input type="text" class="form-control money" name="price_two" required value="7500">
                  </div>

                  <!-- Margen 2 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_two" required value="2500">
                    </div>

                    <!-- Porcentaje 2 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x2" required value="25">
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>x</label>
                    <input type="text" class="form-control numeric" name="cant_two" required value="33">
                  </div>
                </div>

                <!-- Precio 3 -->
                <div class="form-row">

                  <!-- Precio -->
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio 3</strong></label>
                    <input type="text" class="form-control money" name="price_three" required value="7000">
                  </div>

                  <!-- Margen 3 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_three" required value="2000">
                    </div>

                    <!-- Porcentaje 3 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x3" required value="18">
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>x</label>
                    <input type="text" class="form-control numeric" name="cant_three" required value="3">
                  </div>
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