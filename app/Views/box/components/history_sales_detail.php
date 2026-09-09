<div class="card">

  <div class="card-header">
    Detalles de la Venta
    <td>
      <button type="button" class="btn btn-outline-primary history_sales_detail_print">
        <i class="fas fa-print"></i>
      </button>

      <button type="button" class="btn btn-outline-danger history_sales_detail_close">
        <i class="fas fa-times"></i>
      </button>


    </td>
  </div>

  <div class="card-body p-0">

    <div class="table-responsive">

      <table class="table table-hover table-sm table-striped mb-0">

        <thead>

          <tr>

            <th>Codigo</th>
            <th>Cant</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Total</th>

          </tr>

        </thead>

        <tbody>

          <?php if (!empty($sales)): ?>

            <?php foreach ($sales as $row): ?>

              <tr>

                <td>
                  <?= $row['product'] ?>
                </td>
                <td>
                  <?= $row['cant'] ?>
                </td>

                <td>

                  <?= esc($row['descripcion']) ?>

                </td>

                <td class="text-end">

                  <?= number_format(
                    $row['price'],
                    0,
                    ',',
                    '.'
                  ) ?>

                </td>
                <td class="text-end">

                  <?= number_format(
                    $row['total'],
                    0,
                    ',',
                    '.'
                  ) ?>

                </td>


              </tr>

            <?php endforeach ?>

          <?php else: ?>

            <tr>

              <td colspan="5" class="text-center">

                No se encontraron datos

              </td>

            </tr>

          <?php endif ?>

        </tbody>

      </table>

    </div>

  </div>

</div>