<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>Detalles de la Venta</strong>

    <div class="d-flex align-items-center">
      <button type="button" class="btn btn-outline-primary history_sales_detail_print mr-2"
        data-id="<?= esc($saleId) ?>" title="Imprimir venta">
        <i class="fas fa-print"></i>
      </button>

      <button type="button" class="btn btn-outline-danger history_sales_detail_close" title="Cerrar detalles">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-sm table-striped mb-0">
        <thead>
          <tr>
            <th>CODIGO</th>
            <th>CANT</th>
            <th>DESCRIPCIÓN</th>
            <th class="text-right">PRECIO</th>
            <th class="text-right">TOTAL</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($sales)): ?>
            <?php foreach ($sales as $row): ?>
              <tr>
                <td><?= esc($row['product']) ?></td>
                <td><?= esc($row['cant']) ?></td>
                <td><?= esc($row['descripcion']) ?></td>
                <td class="text-right">
                  <?= number_format((float) $row['price'], 0, ',', '.') ?>
                </td>
                <td class="text-right">
                  <?= number_format((float) $row['total'], 0, ',', '.') ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">
                No se encontraron datos
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>