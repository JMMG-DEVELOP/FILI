<div class="card-body p-0">
  <div class="table-responsive carrito-scroll">
    <table class="table table-hover table-striped table-bordered mb-0">
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
              <td><?= $i + 1 ?></td>
              <td><?= $detail['code'] ?></td>
              <td><?= $detail['quantity'] ?></td>
              <td><?= $detail['description'] ?></td>
              <td><?= $detail['price'] ?></td>
              <td><?= $detail['total'] ?></td>
              <td>...</td>
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
</div>