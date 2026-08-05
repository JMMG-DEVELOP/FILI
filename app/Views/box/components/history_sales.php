<div class="card">

  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Historial de ventas</span>

    <small class="text-muted">
      Mostrando <?= count($sales) ?> registros
    </small>
  </div>

  <div class="card-body p-0">

    <div class="table-responsive">

      <table class="table table-hover table-sm table-striped mb-0">

        <thead>

          <tr>
            <th>Hora</th>
            <th>Tipo</th>
            <th>Cliente</th>
            <th class="text-end">Monto</th>
            <th>Pago</th>
            <th>Entrega</th>
            <th>Vuelto</th>
            <th>#</th>
          </tr>

        </thead>

        <tbody>

          <?php if (!empty($sales)): ?>

            <?php foreach ($sales as $row): ?>

              <tr>

                <td><?= date('H:i', strtotime($row['time'])) ?></td>

                <td><?= esc($row['sale_type']) ?></td>

                <td><?= esc($row['customer_name']) ?></td>

                <td class="text-end">
                  <?= number_format($row['total_price'], 0, ',', '.') ?>
                </td>

                <td><?= esc($row['payment_name']) ?></td>

                <td><?= number_format($row['cash_received'], 0, ',', '.') ?></td>

                <td><?= number_format($row['cash_change'], 0, ',', '.') ?></td>

                <td>
                  <button type="buttom" class="btn btn-primary sale_null_id" data-id="<?= esc($row['id']) ?>">
                    <i class="fas fa-cart-plus"></i>
                  </button>
                </td>

              </tr>

            <?php endforeach; ?>

          <?php else: ?>

            <tr>
              <td colspan="7" class="text-center">
                No existen ventas
              </td>
            </tr>

          <?php endif; ?>

        </tbody>

      </table>

    </div>

  </div>

  <div class="card-footer">

    <nav>

      <ul class="pagination pagination-sm justify-content-center mb-0">

        <li class="page-item">
          <button class="page-link" onclick="history_sales_panel_load(1)">
            Primera
          </button>
        </li>

        <li class="page-item">
          <button class="page-link" <?= $page <= 1 ? 'disabled' : '' ?> onclick="history_sales_panel(
            <?= $page - 1 ?>)">
            Anterior
          </button>
        </li>

        <li class="page-item disabled">
          <span class="page-link" id="historyPage">
            Página <?= $page ?> de <?= $totalPages ?>
          </span>
        </li>

        <li class="page-item">
          <button class="page-link" <?= $page >= $totalPages ? 'disabled' : '' ?> onclick="history_sales_panel(
            <?= $page + 1 ?>)">
            Siguiente
          </button>
        </li>

      </ul>

    </nav>

  </div>

</div>