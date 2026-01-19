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
                    <input type="text" class="form-control" name="code" required>
                  </div>

                  <!-- Descripción -->
                  <div class="col-md-5 mb-2">
                    <label>Descripción</label>
                    <input type="text" class="form-control" name="description" required>
                  </div>

                  <!-- Sección -->
                  <div class="col-md-2 mb-2">
                    <label>Sección</label>
                    <select class="form-control select" name="section" id="sections" required>
                      <option value="1">Seleccione</option>
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
                      <option value="1">Seleccione</option>
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
                <legend class="w-auto px-2 font-weight-bold">Forma de Venta</legend>

                <!-- Unidad -->
                <div class="custom-control custom-radio mb-2">
                  <input type="radio" class="custom-control-input" id="sales_1" name="tipo_venta" value="1" required>
                  <label class="custom-control-label" for="sales_1">
                    Venta por <strong>Unidad</strong>
                  </label>
                </div>

                <!-- Kilogramo -->
                <div class="custom-control custom-radio">
                  <input type="radio" class="custom-control-input" id="sales_2" name="tipo_venta" value="2" required>
                  <label class="custom-control-label" for="sales_2">
                    Venta por <strong>Kilogramo</strong>
                  </label>
                </div>
              </fieldset>
            </div>

          </div>

          <div class="row">

            <!-- ==================== STOCK ==================== -->
            <div class="col-md-2">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">Stock</legend>

                <!-- Stock Fili Central -->
                <label>Stock Fili</label>
                <input type="text" class="form-control mb-2" name="stock_1" readonly>

                <label>Edición</label>
                <input type="text" class="form-control stock mb-2" name="edit_stock_1" required>

                <!-- Stock Fili 2 -->
                <label>Stock Fili 2</label>
                <input type="text" class="form-control mb-2" name="stock_2" required>

                <label>Edición</label>
                <input type="text" class="form-control stock" name="edit_stock_2" required>
              </fieldset>
            </div>

            <!-- ==================== COSTOS ==================== -->
            <?php if (can('product_cost_view')): ?>
              <div class="col-md-2">
                <fieldset class="border p-3 mb-3 h-100">
                  <legend class="w-auto px-2 font-weight-bold">Costos</legend>

                  <!-- Costo -->
                  <label>Costo</label>
                  <input type="text" class="form-control money mb-2" name="cost" required>

                  <!-- Último Costo -->
                  <label>Último costo</label>
                  <input type="text" class="form-control money" name="end_cost" required>

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
                    <input type="text" class="form-control money" name="price_one">
                  </div>

                  <!-- Margen 1 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_one">
                    </div>

                    <!-- Porcentaje 1 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x1">
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>Desde</label>
                    <input type="text" class="form-control numeric" name="cant_one" readonly>
                  </div>
                </div>

                <!-- Precio 2 -->
                <div class="form-row">
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio 2</strong></label>
                    <input type="text" class="form-control money" name="price_two">
                  </div>

                  <!-- Margen 2 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_two">
                    </div>

                    <!-- Porcentaje 2 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x2">
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>Desde</label>
                    <input type="text" class="form-control numeric" name="cant_two">
                  </div>
                </div>

                <!-- Precio 3 -->
                <div class="form-row">

                  <!-- Precio -->
                  <div class="col-md-4 mb-2">
                    <label><strong>Precio 3</strong></label>
                    <input type="text" class="form-control money" name="price_three">
                  </div>

                  <!-- Margen 3 -->
                  <?php if (can('product_margin_view')): ?>
                    <div class="col-md-4 mb-2">
                      <label><strong>Margen</strong></label>
                      <input type="text" class="form-control money" name="margin_three">
                    </div>

                    <!-- Porcentaje 3 -->
                    <div class="col-md-2 mb-2">
                      <label>%</label>
                      <input type="text" class="form-control percent" name="x3">
                    </div>
                  <?php endif; ?>

                  <!-- Cantidad Desde -->
                  <div class="col-md-2 mb-2">
                    <label>Desde</label>
                    <input type="text" class="form-control numeric" name="cant_three">
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
                  <option value="1">Seleccione</option>
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