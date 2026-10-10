<?php

$sucursals = [];

if (!empty($values)) {
  foreach ($values as $value) {

    $sucursalId = $value['sucursal_id'];

    if (!isset($sucursals[$sucursalId])) {
      $sucursals[$sucursalId] = [
        'name' => $value['sucursal_name'],
        'values' => []
      ];
    }

    $sucursals[$sucursalId]['values'][] = $value;
  }
}
?>

<div class="col-xl-12 col-lg-6 col-md-12 col-sm-12 col-12">

  <br>

  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

      <h5 class="mb-0">
        Asignación de Dispositivos y Puntos de Expedición
      </h5>

      <?php if (can('point_asignation_add')): ?>

        <a href="#" class="btn btn-outline-primary" id="point_asignation_form_new_open">
          <i class="fas fa-plus"></i>
        </a>

      <?php endif; ?>

    </div>

    <div class="card-body">

      <?php if (!empty($sucursals)): ?>

        <?php $number = 1; ?>

        <?php foreach ($sucursals as $sucursal): ?>

          <div class="card mb-4">

            <div class="card-header">

              <h5 class="mb-0">
                CENTRO DE COMPRAS
                <?= esc($sucursal['name']) ?>
              </h5>

            </div>

            <div class="card-body">

              <div class="table-responsive">

                <table class="table table-striped">

                  <thead>

                    <tr>

                      <th scope="col">
                        #
                      </th>

                      <th scope="col">
                        DISPOSITIVO
                      </th>

                      <th scope="col">
                        UUID
                      </th>

                      <th scope="col">
                        CODIGO
                      </th>

                      <th scope="col">
                        NUMERO INTERNO
                      </th>

                      <th scope="col">
                        NUMERO FISCAL
                      </th>

                      <?php if (
                        can('point_asignation_view') ||
                        can('point_asignation_edit')
                      ): ?>

                        <?php if (can('point_asignation_view')): ?>

                          <th scope="col">
                            Ver
                          </th>

                        <?php endif; ?>

                        <?php if (can('point_asignation_edit')): ?>

                          <th scope="col">
                            Editar
                          </th>

                        <?php endif; ?>

                      <?php endif; ?>

                      <?php if (can('point_asignation_delete')): ?>

                        <th scope="col">
                          Eliminar
                        </th>

                      <?php endif; ?>

                    </tr>

                  </thead>

                  <tbody>

                    <?php foreach ($sucursal['values'] as $value): ?>

                      <tr>

                        <th scope="row">
                          <?= $number++ ?>
                        </th>

                        <td>
                          <?= esc($value['device_name']) ?>
                        </td>

                        <td>
                          <?= esc($value['device_uuid']) ?>
                        </td>

                        <td>
                          <?= esc($value['expedition_point_code']) ?>
                        </td>

                        <td>
                          <?= esc(
                            str_pad(
                              $value['sucursal_id'],
                              3,
                              '0',
                              STR_PAD_LEFT
                            )
                            . ' ' .
                            str_pad(
                              $value['expedition_point_code'],
                              3,
                              '0',
                              STR_PAD_LEFT
                            )
                            . ' ' .
                            str_pad(
                              $value['document_last_number'],
                              7,
                              '0',
                              STR_PAD_LEFT
                            )
                          ) ?>
                        </td>

                        <td>
                          <?= esc(
                            str_pad(
                              $value['sucursal_id'],
                              3,
                              '0',
                              STR_PAD_LEFT
                            )
                            . ' ' .
                            str_pad(
                              $value['expedition_point_code'],
                              3,
                              '0',
                              STR_PAD_LEFT
                            )
                            . ' ' .
                            str_pad(
                              $value['invoice_last_number'],
                              7,
                              '0',
                              STR_PAD_LEFT
                            )
                          ) ?>
                        </td>

                        <?php if (
                          can('point_asignation_view') ||
                          can('point_asignation_edit')
                        ): ?>

                          <?php if (can('point_asignation_view')): ?>

                            <td>

                              <button type="button" class="btn btn-outline-warning point_asignation_form_edit_open"
                                data-id="<?= esc($value['id']) ?>">
                                <i class="fas fa-angle-double-right"></i>
                              </button>

                            </td>

                          <?php endif; ?>

                          <?php if (can('point_asignation_edit')): ?>

                            <td>

                              <button type="button" class="btn btn-outline-warning point_asignation_form_edit_open"
                                data-id="<?= esc($value['id']) ?>">
                                <i class="fas fa-pencil-alt"></i>
                              </button>

                            </td>

                          <?php endif; ?>

                        <?php endif; ?>

                        <?php if (can('point_asignation_delete')): ?>

                          <td>

                            <button type="button" class="btn btn-outline-danger point_asignation_form_delete_open"
                              data-id="<?= esc($value['id']) ?>" data-name="<?= esc($value['device_name']) ?>">
                              <i class="fas fa-trash"></i>
                            </button>

                          </td>

                        <?php endif; ?>

                      </tr>

                    <?php endforeach; ?>

                  </tbody>

                </table>

              </div>

            </div>

          </div>

        <?php endforeach; ?>

      <?php else: ?>

        <div class="text-center py-3">
          NO HAY REGISTROS
        </div>

      <?php endif; ?>

    </div>

  </div>

</div>