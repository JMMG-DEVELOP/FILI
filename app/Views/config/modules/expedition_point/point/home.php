<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

      <h5 class="mb-0">
        Lista de Puntos de Expedición
      </h5>

      <?php if (can('point_point_add')): ?>
        <a href="#" class="btn btn-outline-primary" id="point_point_form_new_open">
          <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>

    </div>

    <div class="card-body">

      <?php if (!empty($values)) { ?>

        <?php

        /*
         * Agrupar puntos por sucursal
         */
        $sucursals = [];

        foreach ($values as $value) {

          $sucursalId = $value['sucursal'];

          if (!isset($sucursals[$sucursalId])) {

            $sucursals[$sucursalId] = [
              'id' => $value['sucursal_id'],
              'name' => $value['sucursal_name'],
              'points' => []
            ];

          }

          $sucursals[$sucursalId]['points'][] = $value;
        }

        ?>

        <div class="row">

          <?php foreach ($sucursals as $sucursal) { ?>

            <!-- CUADRO DE LA SUCURSAL -->
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">

              <div class="card h-100">

                <!-- NOMBRE SUCURSAL -->
                <div class="card-header">

                  <h5 class="mb-0">
                    <?= esc('00' . $sucursal['id'] . '   |  ' . $sucursal['name']) ?>
                  </h5>

                </div>


                <!-- TABLA -->
                <div class="card-body">

                  <div class="table-responsive">

                    <table class="table table-striped table-hover mb-0">

                      <thead>
                        <tr>

                          <th scope="col">
                            #
                          </th>

                          <th scope="col">
                            CÓDIGO
                          </th>

                          <?php if (can('point_point_view') or can('point_point_edit')): ?>

                            <?php if (can('point_point_view')): ?>
                              <th scope="col">
                                Ver
                              </th>
                            <?php endif; ?>

                            <?php if (can('point_point_edit')): ?>
                              <th scope="col">
                                Editar
                              </th>
                            <?php endif; ?>

                          <?php endif; ?>

                          <?php if (can('point_point_delete')): ?>
                            <th scope="col">
                              Eliminar
                            </th>
                          <?php endif; ?>

                        </tr>
                      </thead>


                      <tbody>

                        <?php $number = 1; ?>

                        <?php foreach ($sucursal['points'] as $value) { ?>

                          <tr>

                            <th scope="row">
                              <?= $number++ ?>
                            </th>

                            <td>
                              <?= esc($value['code']) ?>
                            </td>


                            <?php if (can('point_point_view') or can('point_point_edit')): ?>

                              <?php if (can('point_point_view')): ?>

                                <td>

                                  <button type="button" class="btn btn-outline-info point_point_form_view_open"
                                    data-id="<?= esc($value['id']) ?>">
                                    <i class="fas fa-angle-double-right"></i>
                                  </button>

                                </td>

                              <?php endif; ?>


                              <?php if (can('point_point_edit')): ?>

                                <td>

                                  <button type="button" class="btn btn-outline-warning point_point_form_edit_open"
                                    data-id="<?= esc($value['id']) ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                  </button>

                                </td>

                              <?php endif; ?>

                            <?php endif; ?>


                            <?php if (can('point_point_delete')): ?>

                              <td>

                                <button type="button" class="btn btn-outline-danger point_point_form_delete_open"
                                  data-id="<?= esc($value['id']) ?>" data-name="<?= esc($value['code']) ?>">
                                  <i class="fas fa-trash"></i>
                                </button>

                              </td>

                            <?php endif; ?>

                          </tr>

                        <?php } ?>

                      </tbody>

                    </table>

                  </div>

                </div>

              </div>

            </div>

          <?php } ?>

        </div>

      <?php } else { ?>

        <div class="text-center py-3">
          NO HAY REGISTROS
        </div>

      <?php } ?>

    </div>

  </div>

</div>