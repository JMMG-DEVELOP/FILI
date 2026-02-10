<div class="card-body">

  <table class="table table-hover table-striped table-bordered">

    <thead>
      <tr>
        <th>#</th>
        <th>CÓDIGO</th>
        <th>CANT</th>
        <th>DESCRIPCIÓN</th>
        <th>PRECIO</th>
        <th>TOTAL</th>
        <th>OPT</th>
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

</div>