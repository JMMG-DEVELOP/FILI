<div class="card">

  <div class="card-header">
    Movimientos de caja
  </div>

  <div class="card-body p-0">

    <div class="table-responsive">

      <table class="table table-hover table-sm table-striped mb-0">

        <thead>

          <tr>

            <th>#</th>
            <th>Tipo</th>
            <th>Monto</th>

          </tr>

        </thead>

        <tbody>

          <?php if (!empty($movements)): ?>

            <?php foreach ($movements as $row): ?>

              <tr>

                <td>
                  <?= $row['id'] ?>
                </td>

                <td>

                  <?php if ($row['sales'] != null): ?>

                    <span class="badge bg-success">
                      Venta
                    </span>

                  <?php else: ?>

                    <?= esc($row['movement_type_name']) ?>

                  <?php endif ?>

                </td>


                <td class="text-end">

                  <?= number_format(
                    $row['mount'],
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

                No existen movimientos

              </td>

            </tr>

          <?php endif ?>

        </tbody>

      </table>

    </div>

  </div>

</div>