<div class="card">

  <div class="card-header d-flex justify-content-between align-items-center">

    <h5 class="mb-0">
      Detalles de la venta #<?= esc($sale_id) ?>
    </h5>

    <button type="button" class="btn btn-outline-danger" id="sales_detail_close" title="Cerrar detalles">
      <i class="fas fa-times"></i>
    </button>

  </div>

  <div class="card-body">

    <div class="table-responsive w-100">

      <table id="table_sales_detail" class="table table-striped table-bordered nowrap w-100">

        <thead>
          <tr>
            <th>CODIGO</th>
            <th>CANT</th>
            <th>DESCRIPCIÓN</th>
            <th>PRECIO</th>
            <th>TOTAL</th>
          </tr>
        </thead>

        <tbody>

          <?php foreach ($details as $detail): ?>

            <tr>
              <td>
                <?= esc($detail['product']) ?>
              </td>

              <td>
                <?= esc($detail['cant']) ?>
              </td>

              <td>
                <?= esc($detail['descripcion'] ?? '') ?>
              </td>

              <td class="text-end">
                ₲ <?= number_format(
                  (float) $detail['price'],
                  0,
                  ',',
                  '.'
                ) ?>
              </td>

              <td class="text-end">
                ₲ <?= number_format(
                  (float) $detail['total'],
                  0,
                  ',',
                  '.'
                ) ?>
              </td>
            </tr>

          <?php endforeach; ?>

        </tbody>

      </table>

    </div>

  </div>

</div>