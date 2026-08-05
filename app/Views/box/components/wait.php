<div class="card">

  <div class="card-header">
    Clientes en Espera
  </div>

  <div class="card-body p-0">

    <div class="table-responsive">

      <table class="table table-hover table-sm table-striped mb-0">

        <thead>

          <tr>

            <th>FECHA</th>
            <th>CLIENTE</th>
            <th>MONTO</th>
            <th>USUARIO</th>
            <th>OPCIONES</th>

          </tr>

        </thead>

        <tbody>
          <?php if (!empty($data)): ?>

            <?php foreach ($data as $row): ?>

              <tr>

                <td>
                  <?= $row['date'] . ' ' . $row['time'] ?>
                </td>

                <td>
                  <?= $row['customer']; ?>

                </td>


                <td class="text-end">

                  <?= number_format(
                    $row['mount'],
                    0,
                    ',',
                    '.'
                  ) ?>

                </td>
                <td>
                  <?= $row['name']; ?>

                </td>
                <td>

                  <button type="buttom" class="btn btn-primary wait_id" data-id="<?= esc($row['id']) ?>">
                    <i class="fas fa-cart-plus"></i>
                  </button>

                </td>

              </tr>

            <?php endforeach ?>

          <?php else: ?>

            <tr>

              <td colspan="5" class="text-center">

                No existen Clientes en Espera

              </td>

            </tr>

          <?php endif ?>

        </tbody>

      </table>

    </div>

  </div>

</div>