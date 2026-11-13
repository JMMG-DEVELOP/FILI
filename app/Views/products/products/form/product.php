<?php

$type = $type ?? 'new';

$isEdit = $type === 'edit';

$product = $product ?? null;

$prices = $product['prices'] ?? [];

$stock = $product['stock'] ?? [];

$code = $code ?? '';

?>

<div class="row justify-content-center">

  <div class="col-xl-12 col-lg-12 col-md-12">

    <div class="card shadow-sm">

      <!-- ==================== HEADER ==================== -->

      <div class="card-header bg-light text-center">

        <div class="border-top pt-2 text-right">

          <button type="button" class="btn btn-outline-danger <?= ($page ?? '') === 'shopp'
            ? 'shopp_product_form_close'
            : 'product_form_close' ?>">
            <i class="fas fa-times"></i>
          </button>

        </div>

        <h3 class="mb-0 font-weight-bold">
          <?= esc($title ?? '') ?>
        </h3>

      </div>

      <!-- ==================== BODY ==================== -->

      <div class="card-body">

        <form id="product_form">

          <?= csrf_field() ?>

          <input type="hidden" name="operation_type" id="operation_type" value="<?= esc($type) ?>">

          <div class="row">

            <!-- ==================== DESCRIPCIÓN ==================== -->

            <div class="col-md-12">

              <fieldset class="border p-3 mb-3 h-100">

                <legend class="w-auto px-2 font-weight-bold">
                  Descripción
                </legend>

                <div class="form-row">
                  <!-- Código -->
                  <div class="col-md-2 mb-2">
                    <label for="code">
                      Código
                    </label>
                    <input type="text" class="form-control" name="code" id="code"
                      value="<?= esc($product['code'] ?? $code) ?>" <?= $isEdit ? 'readonly' : '' ?> required>
                  </div>

                  <!-- Descripción -->

                  <div class="col-md-4 mb-2">
                    <label for="description">
                      Descripción
                    </label>
                    <input type="text" class="form-control" name="description" id="description"
                      value="<?= esc($product['description'] ?? '') ?>" required autofocus>
                  </div>

                  <!-- Sección -->

                  <div class="col-md-2 mb-2">
                    <label for="sections">
                      Sección
                    </label>
                    <div id="windows_section"></div>
                    <select class="form-control select" name="section" id="sections" required>
                      <option value="">
                        Seleccione
                      </option>
                      <?php foreach ($sections as $section): ?>
                        <option value="<?= esc($section['id']) ?>" <?= (string) ($product['section_id'] ?? '') === (string) $section['id']
                            ? 'selected'
                            : '' ?>>
                          <?= esc($section['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Marca -->
                  <div class="col-md-2 mb-2">
                    <label for="brands">
                      Marca
                    </label>
                    <select class="form-control select" name="brand" id="brands" required>
                      <option value="">
                        Seleccione
                      </option>
                      <?php foreach ($brands as $brand): ?>
                        <option value="<?= esc($brand['id']) ?>" <?= (string) ($product['brand_id'] ?? '') === (string) $brand['id']
                            ? 'selected'
                            : '' ?>>
                          <?= esc($brand['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Forma de Venta -->
                  <div class="col-md-2 mb-2">
                    <label for="sales">
                      Forma de venta
                    </label>
                    <select class="form-control select" name="sales" id="sales" required>
                      <option value="">
                        Seleccione
                      </option>
                      <?php foreach ($sales as $sale): ?>
                        <option value="<?= esc($sale['id']) ?>" <?= (string) ($product['sales_id'] ?? '') === (string) $sale['id']
                            ? 'selected'
                            : '' ?>>
                          <?= esc($sale['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                </div>
              </fieldset>
            </div>
          </div>

          <div class="row">

            <!-- ==================== COSTOS ==================== -->

            <?php if (can('product_cost_view')): ?>
              <div class="col-md-2">
                <fieldset class="border p-3 mb-3 h-100">
                  <legend class="w-auto px-2 font-weight-bold">
                    Costo
                  </legend>
                  <label for="cost">
                    Costo
                  </label>
                  <input type="text" class="form-control guaranies mb-2" name="cost" id="cost"
                    value="<?= esc($product['cost'] ?? '') ?>" required>
                </fieldset>
              </div>
            <?php endif; ?>

            <!-- ==================== VENTAS ==================== -->

            <div class="col-md-8">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">
                  Venta
                </legend>

                <!-- PRECIO 1 -->
                <div class="form-row">
                  <div class="col-md-4 mb-2">
                    <label for="price_one">
                      <strong>Precio 1</strong>
                    </label>
                    <input type="text" class="form-control guaranies" name="price_one" id="price_one"
                      value="<?= esc($prices['price_one'] ?? '') ?>" required>
                  </div>
                  <?php if (can('product_margin_view')): ?>

                    <!-- Margen 1 -->
                    <div class="col-md-4 mb-2">
                      <label>
                        <strong>Margen</strong>
                      </label>
                      <input type="text" class="form-control guaranies" name="margin_one" value="" required>
                    </div>

                    <!-- Porcentaje 1 -->
                    <div class="col-md-2 mb-2">
                      <label>
                        %
                      </label>
                      <input type="text" class="form-control percent" name="x1" id="x1" value="" required>
                    </div>

                  <?php endif; ?>
                </div>

                <!-- PRECIO 2 -->
                <div class="form-row">

                  <div class="col-md-4 mb-2">
                    <label for="price_two">
                      <strong>Precio 2</strong>
                    </label>
                    <input type="text" class="form-control guaranies" name="price_two" id="price_two"
                      value="<?= esc($prices['price_two'] ?? '') ?>" required>
                  </div>

                  <?php if (can('product_margin_view')): ?>

                    <!-- Margen 2 -->
                    <div class="col-md-4 mb-2">
                      <label>
                        <strong>Margen</strong>
                      </label>
                      <input type="text" class="form-control guaranies" name="margin_two" value="" required>
                    </div>

                    <!-- Porcentaje 2 -->
                    <div class="col-md-2 mb-2">
                      <label>
                        %
                      </label>
                      <input type="text" class="form-control percent" name="x2" value="" required>
                    </div>

                  <?php endif; ?>

                  <!-- Cantidad Desde 2 -->
                  <div class="col-md-2 mb-2">
                    <label for="cant_two">
                      Cant
                    </label>
                    <input type="text" class="form-control number" name="cant_two" id="cant_two"
                      value="<?= esc($prices['cant_two'] ?? '') ?>" required>
                  </div>
                </div>

                <!-- PRECIO 3 -->
                <div class="form-row">

                  <div class="col-md-4 mb-2">
                    <label for="price_three">
                      <strong>Precio 3</strong>
                    </label>
                    <input type="text" class="form-control guaranies" name="price_three" id="price_three"
                      value="<?= esc($prices['price_three'] ?? '') ?>" required>
                  </div>

                  <?php if (can('product_margin_view')): ?>

                    <!-- Margen 3 -->
                    <div class="col-md-4 mb-2">
                      <label>
                        <strong>Margen</strong>
                      </label>
                      <input type="text" class="form-control guaranies" name="margin_three" value="" required>
                    </div>

                    <!-- Porcentaje 3 -->
                    <div class="col-md-2 mb-2">
                      <label>
                        %
                      </label>
                      <input type="text" class="form-control percent" name="x3" value="" required>
                    </div>

                  <?php endif; ?>

                  <!--Cantidad Desde 3 -->
                  <div class="col-md-2 mb-2">
                    <label for="cant_three">
                      Cant
                    </label>
                    <input type="text" class="form-control number" name="cant_three" id="cant_three"
                      value="<?= esc($prices['cant_three'] ?? '') ?>" required>
                  </div>
                </div>

              </fieldset>
            </div>

            <!-- ==================== IVA ==================== -->

            <div class="col-md-2">
              <fieldset class="border p-3 mb-3 h-100">
                <legend class="w-auto px-2 font-weight-bold">
                  IVA
                </legend>
                <label for="iva">
                  %
                </label>
                <select class="form-control select" name="iva" id="iva" required>
                  <?php foreach ($ivas as $iva): ?>
                    <option value="<?= esc($iva['id']) ?>" <?= (string) ($product['iva_id'] ?? 1) === (string) $iva['id']
                        ? 'selected'
                        : '' ?>>
                      <?= esc($iva['name']) ?> %
                    </option>
                  <?php endforeach; ?>
                </select>
              </fieldset>
            </div>
          </div>

          <div class="row">

            <!-- STOCK -->
            <?php if (can('product_stock_view')): ?>

              <div class="col-md-12">

                <fieldset class="border p-3 mb-3 h-100">

                  <legend class="w-auto px-2 font-weight-bold">
                    Stock por sucursal
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
          <!-- ==================== FOOTER ==================== -->
          <div class="border-top pt-3 text-right">
            <?php
            $canSaveProduct = $isEdit
              ? can('product_product_edit')
              : can('product_product_add');
            ?>

            <?php if ($canSaveProduct): ?>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-check"></i>
                <?= $isEdit ? 'Actualizar' : 'Guardar ' ?>
              </button>
            <?php endif; ?>

            <button type="button" class="btn btn-outline-danger product_form_close">
              <i class="fas fa-times"></i>
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>