<!-- <div class="card-body"> -->

<table class="table table-hover table-striped table-bordered">

  <thead>
    <tr>
      <th>#</th>
      <th>C.I. / RUC</th>
      <th>NOMBRE / DENOMINACIÓN</th>
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

          <!-- C.I. / RUC -->
          <td class="text-center">
            <span class="badge bg-secondary">
              <?= esc($detail['ci']) ?>
            </span>
          </td>

          <!-- Descripción -->
          <td>
            <?= esc($detail['name']) ?>
          </td>

          <!-- Botón agregar -->
          <td class="text-center">

            <button class="btn btn-sm btn-primary add_customer_form" data-ci="<?= esc($detail['ci']) ?>"
              data-id="<?= esc($detail['id']) ?>" data-name="<?= esc($detail['name']) ?>">
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

</div>
<div class="border-top pt-4 text-right">
  <button type="button" class="btn btn-outline-light add_new_customer">
    Agregar Nuevo
  </button>

  <button type="button" class="btn btn-outline-danger customer_search_cancel">
    <i class="fas fa-times"></i>
  </button>
</div>