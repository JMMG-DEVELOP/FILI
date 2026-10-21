<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">
  <div class="card">
    <?php $number = 1; ?>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="mb-0"> Detalles de la Venta </h3>
      <a href="#" class="btn btn-outline-danger" id="sales details_print"> <i class="fas fa-print"></i>
      </a>
      <a href="#" class="btn btn-outline-danger" id="details_close" data-id="<?= $customer ?>"> <i
          class="fas fa-times"></i>
      </a>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped" id="table_details">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">CODIGO</th>
              <th scope="col">DESCRIPCIÓN</th>
              <th scope="col">CANT</th>
              <th scope="col">P.U.</th>
              <th scope="col">TOTAL</th>
              <th scope="col">SUCURSAL</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($values)) { ?>
              <?php foreach ($values as $value) { ?>
                <tr>
                  <th scope="row">
                    <?= $number++ ?>
                  </th>
                  <td>
                    <?= esc($value['product']) ?>
                  </td>
                  <td>
                    <?= esc($value['descripcion']) ?>
                  </td>
                  <td>
                    <?= esc($value['cant']) ?>
                  </td>
                  <td>
                    <?= esc($value['price']) ?>
                  </td>
                  <td>
                    <?= esc($value['total']) ?>
                  </td>
                  <td>
                    <?= esc($value['sucursal']) ?>
                  </td>
                </tr>
              <?php } ?>
            <?php } ?>
          </tbody>
        </table>

        <?php if (empty($values)) { ?>
          <div class="text-center py-3">
            NO HAY REGISTROS
          </div>
        <?php } ?>
      </div>
    </div>

  </div>
</div>