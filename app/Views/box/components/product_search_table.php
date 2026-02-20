<div class="col-md-12">

  <table class="table table-hover table-striped table-bordered">

    <thead>
      <tr>
        <th>#</th>
        <th>CÓDIGO</th>
        <th>DESCRIPCIÓN</th>
        <th>PRECIO</th>

        <!-- Sucursales dinámicas -->
        <?php if (!empty($result)):
          $first = $result[0];
          foreach ($first['stock'] as $sucursalId => $qty): ?>
            <th>FILI <?= $sucursalId ?></th>
          <?php endforeach;
        endif; ?>

        <th>AGREGAR</th>
      </tr>
    </thead>

    <tbody>

      <?php if (!empty($result)): ?>
        <?php foreach ($result as $i => $detail): ?>

          <tr>

            <!-- Índice -->
            <td class="text-center fw-bold">
              <?= $i + 1 ?>
            </td>

            <!-- Código -->
            <td class="text-center">
              <span class="badge bg-secondary">
                <?= esc($detail['code']) ?>
              </span>
            </td>

            <!-- Descripción -->
            <td>
              <?= esc($detail['description']) ?>
            </td>

            <!-- Precio -->
            <td class="text-end fw-bold text-success">
              <?= number_format($detail['price_one'], 0, ',', '.') ?>
            </td>

            <!-- Stock por sucursal -->
            <?php foreach ($detail['stock'] as $qty): ?>
              <td class="text-center">
                <?= number_format($qty, 0, ',', '.') ?>
              </td>
            <?php endforeach; ?>

            <!-- Botón agregar -->
            <td class="text-center">

              <button class="btn btn-sm btn-primary add_product_cart_search" data-code="<?= esc($detail['code']) ?>">
                <i class="fas fa-plus"></i>
              </button>

            </td>

          </tr>

        <?php endforeach; ?>
      <?php else: ?>

        <tr>
          <td colspan="100" class="text-center text-muted py-4">
            Sin resultados en la tabla
          </td>
        </tr>

      <?php endif; ?>

    </tbody>

  </table>

  <div class="border-top pt-4 text-center">
    <button type="button" class="btn btn-outline-danger product_search_cancel">
      X
    </button>
  </div>
</div>