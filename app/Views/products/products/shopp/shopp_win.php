<!-- STOCK -->
<div class="row">

  <!-- STOCK -->
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

              <div class="border rounded p-3 h-100">

                <h6 class="font-weight-bold mb-3">

                  <i class="fas fa-store text-primary"></i>

                  <?= esc($sucursal['name']) ?>

                </h6>

                <div class="form-row align-items-end">

                  <!-- STOCK ACTUAL -->

                  <div class="<?= can('product_stock_edit') ? 'col-6' : 'col-12' ?>">

                    <label for="stock_<?= $sucursalId ?>">
                      Stock actual
                    </label>

                    <input type="text" class="form-control stock" name="stock[<?= $sucursalId ?>]"
                      id="stock_<?= $sucursalId ?>" value="<?= esc($currentStock) ?>" readonly>

                  </div>

                  <!-- MODIFICAR STOCK -->

                  <?php if (can('product_stock_edit')): ?>

                    <div class="col-6">

                      <label for="edit_stock_<?= $sucursalId ?>">
                        Modificar
                      </label>

                      <input type="text" class="form-control stock" name="edit_stock[<?= $sucursalId ?>]"
                        id="edit_stock_<?= $sucursalId ?>" value="" placeholder="Ajuste" autocomplete="off">

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
          <input type="text" class="form-control form-control-sm guaranies" name="price_shop" id="price_shop" required>
        </div>

        <!-- Cantidad -->
        <div class="col-md-2 mb-2">
          <label for="quantity_shop">Cant.</label>
          <input type="text" class="form-control form-control-sm number" name="quantity_shop" id="quantity_shop"
            value="1">
        </div>

        <!-- Proveedor -->
        <div class="col-md-4 mb-2">
          <label for="supplier">Proveedor</label>

          <select class="form-control form-control-sm select" name="supplier" id="supplier" required>
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

          <select class="form-control form-control-sm select" name="supplier" id="supplier" required>
            <option value="">Seleccione</option>

            <?php foreach ($sucursal as $item): ?>
              <option value="<?= esc($item['id']) ?>">
                <?= esc($item['name']) ?>
              </option>
            <?php endforeach; ?>

          </select>
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
          <input type="text" class="form-control form-control-sm guaranies" name="cost" id="cost">
        </div>

        <!-- Costo actualizado -->
        <div class="col-md-4 mb-2">
          <label for="updated_cost">Actualizado</label>
          <input type="text" class="form-control form-control-sm guaranies" name="updated_cost" id="updated_cost"
            readonly>
        </div>

        <!-- Calcular -->
        <div class="col-md-4 mb-2">
          <button type="button" class="btn btn-outline-success btn-sm btn-block">
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
          <label for="price_one"><strong>Precio 1</strong></label>
          <input type="text" class="form-control guaranies" name="price_one" id="price_one"
            value="<?= esc($prices['price_one'] ?? '') ?>" required>
        </div>

        <?php if (can('product_margin_view')): ?>

          <div class="col-md-4 mb-2">
            <label><strong>Margen</strong></label>
            <input type="text" class="form-control guaranies" name="margin_one" value="" required>
          </div>

          <div class="col-md-2 mb-2">
            <label>%</label>
            <input type="text" class="form-control percent" name="x1" id="x1" value="" required>
          </div>

        <?php endif; ?>

      </div>

      <!-- PRECIO 2 -->
      <div class="form-row">

        <div class="col-md-4 mb-2">
          <label for="price_two"><strong>Precio 2</strong></label>
          <input type="text" class="form-control guaranies" name="price_two" id="price_two"
            value="<?= esc($prices['price_two'] ?? '') ?>" required>
        </div>

        <?php if (can('product_margin_view')): ?>

          <div class="col-md-4 mb-2">
            <label><strong>Margen</strong></label>
            <input type="text" class="form-control guaranies" name="margin_two" value="" required>
          </div>

          <div class="col-md-2 mb-2">
            <label>%</label>
            <input type="text" class="form-control percent" name="x2" value="" required>
          </div>

        <?php endif; ?>

        <div class="col-md-2 mb-2">
          <label for="cant_two">Cant.</label>
          <input type="text" class="form-control number" name="cant_two" id="cant_two"
            value="<?= esc($prices['cant_two'] ?? '') ?>" required>
        </div>

      </div>

      <!-- PRECIO 3 -->
      <div class="form-row">

        <div class="col-md-4 mb-2">
          <label for="price_three"><strong>Precio 3</strong></label>
          <input type="text" class="form-control guaranies" name="price_three" id="price_three"
            value="<?= esc($prices['price_three'] ?? '') ?>" required>
        </div>

        <?php if (can('product_margin_view')): ?>

          <div class="col-md-4 mb-2">
            <label><strong>Margen</strong></label>
            <input type="text" class="form-control guaranies" name="margin_three" value="" required>
          </div>

          <div class="col-md-2 mb-2">
            <label>%</label>
            <input type="text" class="form-control percent" name="x3" value="" required>
          </div>

        <?php endif; ?>

        <div class="col-md-2 mb-2">
          <label for="cant_three">Cant.</label>
          <input type="text" class="form-control number" name="cant_three" id="cant_three"
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
            <button type="button" class="btn btn-outline-success btn-sm btn-block" id="print_barcode">
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
            <button type="button" class="btn btn-outline-success btn-sm btn-block" id="print_label">
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

  <button type="submit" class="btn btn-primary">
    <i class="fas fa-check"></i>
  </button>

</div>