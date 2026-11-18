<form id="shopp_form">
  <div class="row">
    <div class="col-md-12">
      <div class="border-top pt-3 text-right">
        <button type="buttom" class="btn btn-danger cancel_shopp" hidden>
          <i class="fas fa-times"> </i>
        </button>
      </div>
      <fieldset class="border p-3 mb-3 h-100">
        <legend class="w-auto px-2 font-weight-bold">
          Descripción
        </legend>
        <div class="form-row">
          <!-- Código -->
          <div class="col-md-4 mb-2">
            <label for="shop_code">
              Código
            </label>
            <input type="text" class="form-control shopp_search" name="shopp_code" id="shopp_code" autofocus required>
          </div>

          <!-- Descripción -->
          <div class="col-md-8 mb-2">
            <label for="description">
              Descripción
            </label>
            <input type="text" class="form-control shopp_search" name="description" id="shopp_description">
          </div>

        </div>

        <div id="shop_search_result"></div>

      </fieldset>
    </div>
  </div>
  <div id="shopp_win">
    <!-- STOCK -->

    <div id="shopp_stock_win">
      <div class="row">

        <?php if (can('product_stock_view')): ?>

          <div class="col-md-12">

            <fieldset class="border p-3 mb-3 h-100">

              <legend class="w-auto px-2 font-weight-bold">
                Stock
              </legend>

              <div class="row">

                <?php foreach (($sucursals ?? []) as $sucursal): ?>

                  <?php
                  $sucursalId = (int) $sucursal['id'];
                  $currentStock = $stock[$sucursalId] ?? 0;
                  ?>

                  <div class="col-xl-3 col-lg-4 col-md-6 mb-3">

                    <div class="border rounded p-3 h-100 sucursal-stock">

                      <h6 class="font-weight-bold mb-3">
                        <i class="fas fa-store text-primary"></i>
                        <?= esc($sucursal['name']) ?>
                      </h6>

                      <div class="form-row align-items-end">

                        <!-- STOCK ACTUAL -->
                        <div class="<?= can('product_stock_edit') ? 'col-4' : 'col-12' ?>">

                          <label for="stock_<?= $sucursalId ?>">
                            Stock actual
                          </label>

                          <input type="text" class="form-control stock" name="stock[<?= $sucursalId ?>]"
                            id="stock_<?= $sucursalId ?>" value="<?= esc($currentStock) ?>" readonly>

                        </div>

                        <!-- MODIFICAR STOCK -->
                        <?php if (can('product_stock_edit')): ?>

                          <div class="col-4">

                            <label for="edit_stock_<?= $sucursalId ?>">
                              Modificar
                            </label>

                            <input type="number" step="0.001" class="form-control stock edit_stock"
                              name="edit_stock[<?= $sucursalId ?>]" id="edit_stock_<?= $sucursalId ?>" autocomplete="off">

                          </div>

                          <!-- BOTÓN DE ACTUALIZACIÓN -->
                          <div class="col-4">

                            <button type="button" class="btn btn-primary btn-block shopp_stock_save"
                              data-sucursal="<?= $sucursalId ?>" hidden>
                              <i class="fas fa-check"></i>
                            </button>

                          </div>

                        <?php endif; ?>

                      </div>

                    </div>

                  </div>

                <?php endforeach; ?>

              </div>

            </fieldset>

          </div>

        <?php endif; ?>

      </div>
    </div>

    <div class="row">
      <!-- ================= COMPRA ================= -->
      <div class="col-md-8">
        <fieldset class="border p-2 mb-3 h-100">

          <legend class="w-auto px-2 font-weight-bold">
            Compra
          </legend>

          <div class="form-row align-items-end">

            <!-- Precio -->
            <div class="col-md-2 mb-2">
              <label for="price_shop">Precio</label>
              <input type="text" class="form-control form-control-sm guaranies" name="price_shop" id="price_shop"
                required>
            </div>

            <!-- Cantidad -->
            <div class="col-md-2 mb-2">
              <label for="quantity_shop">Cant.</label>
              <input type="text" class="form-control form-control-sm number" name="quantity_shop" id="quantity_shop"
                required>
            </div>

            <!-- Proveedor -->
            <div class="col-md-4 mb-2">
              <label for="supplier">Proveedor</label>

              <select class="form-control form-control-sm select" name="provider" id="provider" required>
                <option value="">Seleccione</option>

                <?php foreach ($provider as $item): ?>
                  <option value="<?= esc($item['id']) ?>">
                    <?= esc($item['name']) ?>
                  </option>
                <?php endforeach; ?>

              </select>
            </div>
            <!-- Sucursal -->
            <div class="col-md-4 mb-2">
              <label for="supplier">Sucursal</label>

              <select class="form-control form-control-sm select" name="sucursal" id="sucursal" required>
                <option value="">Seleccione</option>

                <?php foreach ($sucursals as $item): ?>
                  <option value="<?= esc($item['id']) ?>">
                    <?= esc($item['name']) ?>
                  </option>
                <?php endforeach; ?>

              </select>
            </div>

          </div>
          <div class="form-row align-items-end">
            <!-- Precio -->
            <div class="col-md-2 mb-2">
              <label for="price_shop_history">Precio</label>
              <input type="text" class="form-control form-control-sm guaranies" name="price_shop_history"
                id="price_shop_history" readonly>
            </div>

            <!-- Cantidad -->
            <div class="col-md-1 mb-2">
              <label for="quantity_shop_history">Cant.</label>
              <input type="text" class="form-control form-control-sm number" name="quantity_shop_history"
                id="quantity_shop_history" readonly>
            </div>
            <!-- Proveedor -->
            <div class="col-md-4 mb-2">
              <label for="price_shop">Proveedor</label>
              <input type="text" class="form-control form-control-sm " name="cost_shop_history" id="cost_shop_history"
                readonly>
            </div>

            <!-- Usuario -->
            <div class="col-md-3 mb-2">
              <label for="user_shop_history">Usuario</label>
              <input type="text" class="form-control form-control-sm" name="user_shop_history" id="user_shop_history"
                readonly>
            </div>
            <!-- Fecha -->
            <div class="col-md-2 mb-2">
              <label for="date_shop_history">Fecha</label>
              <input type="text" class="form-control form-control-sm" name="date_shop_history" id="date_shop_history"
                readonly>
            </div>
          </div>
        </fieldset>
      </div>

      <!-- ================= COSTO ================= -->
      <div class="col-md-4">
        <fieldset class="border p-2 mb-3 h-100">

          <legend class="w-auto px-2 font-weight-bold">
            Costo
          </legend>

          <div class="form-row align-items-end">

            <!-- Costo -->
            <div class="col-md-4 mb-2">
              <label for="cost">Costo</label>
              <input type="text" class="form-control form-control-sm guaranies" id="cost" required>
            </div>

            <!-- Costo actualizado -->
            <div class="col-md-4 mb-2">
              <label for="updated_cost">Actualizado</label>
              <input type="text" class="form-control form-control-sm guaranies" name="cost" readonly required>
            </div>

            <!-- Calcular -->
            <div class="col-md-4 mb-2">
              <button type="button" class="btn btn-outline-success btn-sm btn-block calculate_cost" hidden>
                Calcular
              </button>
            </div>

          </div>

        </fieldset>
      </div>
    </div>

    <div class="row">

      <!-- ================================================= -->
      <!-- VENTA - 8 COLUMNAS -->
      <!-- ================================================= -->

      <div class="col-md-8">

        <fieldset class="border p-3 mb-3 h-100">

          <legend class="w-auto px-2 font-weight-bold">
            Venta
          </legend>

          <!-- PRECIO 1 -->
          <div class="form-row">

            <div class="col-md-4 mb-2">
              <label for="shopp_price_one"><strong>Precio 1</strong></label>
              <input type="text" class="form-control guaranies" name="shopp_price_one" id="shopp_price_one"
                value="<?= esc($prices['price_one'] ?? '') ?>" required>
            </div>

            <?php if (can('product_margin_view')): ?>

              <div class="col-md-4 mb-2">
                <label><strong>Margen</strong></label>
                <input type="text" class="form-control guaranies" name="shopp_margin_one" value="" required>
              </div>

              <div class="col-md-2 mb-2">
                <label>%</label>
                <input type="text" class="form-control percent" name="shopp_x1" id="shopp_x1" value="" required>
              </div>

            <?php endif; ?>

          </div>

          <!-- PRECIO 2 -->
          <div class="form-row">

            <div class="col-md-4 mb-2">
              <label for="shopp_price_two"><strong>Precio 2</strong></label>
              <input type="text" class="form-control guaranies" name="shopp_price_two" id="shopp_price_two"
                value="<?= esc($prices['price_two'] ?? '') ?>" required>
            </div>

            <?php if (can('product_margin_view')): ?>

              <div class="col-md-4 mb-2">
                <label><strong>Margen</strong></label>
                <input type="text" class="form-control guaranies" name="shopp_margin_two" value="" required>
              </div>

              <div class="col-md-2 mb-2">
                <label>%</label>
                <input type="text" class="form-control percent" name="shopp_x2" value="" required>
              </div>

            <?php endif; ?>

            <div class="col-md-2 mb-2">
              <label for="cant_two">Cant.</label>
              <input type="text" class="form-control number" name="shopp_cant_two" id="shopp_cant_two"
                value="<?= esc($prices['cant_two'] ?? '') ?>" required>
            </div>

          </div>

          <!-- PRECIO 3 -->
          <div class="form-row">

            <div class="col-md-4 mb-2">
              <label for="shopp_price_three"><strong>Precio 3</strong></label>
              <input type="text" class="form-control guaranies" name="shopp_price_three" id="shopp_price_three"
                value="<?= esc($prices['price_three'] ?? '') ?>" required>
            </div>

            <?php if (can('product_margin_view')): ?>

              <div class="col-md-4 mb-2">
                <label><strong>Margen</strong></label>
                <input type="text" class="form-control guaranies" name="shopp_margin_three" value="" required>
              </div>

              <div class="col-md-2 mb-2">
                <label>%</label>
                <input type="text" class="form-control percent" name="shopp_x3" value="" required>
              </div>

            <?php endif; ?>

            <div class="col-md-2 mb-2">
              <label for="shopp_cant_three">Cant.</label>
              <input type="text" class="form-control number" name="shopp_cant_three" id="shopp_cant_three"
                value="<?= esc($prices['cant_three'] ?? '') ?>" required>
            </div>

          </div>

        </fieldset>

      </div>


      <!-- ================================================= -->
      <!-- IMPRESIÓN - 4 COLUMNAS -->
      <!-- ================================================= -->

      <div class="col-md-4">

        <fieldset class="border p-3 mb-3 h-100">

          <legend class="w-auto px-2 font-weight-bold">
            Impresión
          </legend>

          <!-- CÓDIGO DE BARRAS -->
          <fieldset class="border p-2 mb-3">

            <legend class="w-auto px-2 font-weight-bold">
              Código de Barras
            </legend>

            <div class="form-row align-items-end">

              <div class="col-7 mb-2">
                <label for="cant_code_print">Cantidad</label>
                <input type="number" class="form-control form-control-sm" name="cant_code_print" id="cant_code_print"
                  min="1" value="1">
              </div>

              <div class="col-5 mb-2">
                <button type="button" class="btn btn-outline-success btn-sm btn-block print_barcode" id="print_barcode"
                  hidden>
                  <i class="fas fa-print"></i>
                </button>
              </div>

            </div>

          </fieldset>

          <!-- ETIQUETA -->
          <fieldset class="border p-2 mb-2">

            <legend class="w-auto px-2 font-weight-bold">
              Etiqueta
            </legend>

            <div class="form-row align-items-end">

              <div class="col-7 mb-2">
                <label for="cant_label_print">Cantidad</label>
                <input type="number" class="form-control form-control-sm" name="cant_label_print" id="cant_label_print"
                  min="1" value="1">
              </div>

              <div class="col-5 mb-2">
                <button type="button" class="btn btn-outline-success btn-sm btn-block print_label" id="print_label"
                  hidden>
                  <i class="fas fa-print"></i>
                </button>
              </div>

            </div>

          </fieldset>

          <!-- NOMBRE -->
          <fieldset class="border p-2 mb-2">

            <legend class="w-auto px-2 font-weight-bold">
              Nombre
            </legend>

            <div class="form-row align-items-end">

              <div class="col-7 mb-2">
                <label for="cant_label_print">Cantidad</label>
                <input type="number" class="form-control form-control-sm" name="cant_label_print" id="cant_name_print"
                  min="1" value="1">
              </div>

              <div class="col-5 mb-2">
                <button type="button" class="btn btn-outline-success btn-sm btn-block print_name" id="print_label"
                  hidden>
                  <i class="fas fa-print"></i>
                </button>
              </div>

            </div>

          </fieldset>

        </fieldset>

      </div>

    </div>
    <!-- ==================== FOOTER ==================== -->
    <div class="border-top pt-3 text-right">

      <button type="buttom" class="btn btn-danger cancel_shopp" hidden>
        <i class="fas fa-times"> </i>
      </button>
      <button type="buttom" class="btn btn-primary send_shopp" hidden>
        <i class="fas fa-check"> REGISTRAR </i>
      </button>

    </div>
  </div>
</form>