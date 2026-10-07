<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

  <?php
  $invoiceSucursals = [];

  if (!empty($invoice)) {
    foreach ($invoice as $value) {
      $sucursalId = $value['sucursal_id'];

      if (!isset($invoiceSucursals[$sucursalId])) {
        $invoiceSucursals[$sucursalId] = [
          'id' => $value['sucursal_id'],
          'name' => $value['sucursal_name'],
          'items' => []
        ];
      }

      $invoiceSucursals[$sucursalId]['items'][] = $value;
    }
  }

  $documentSucursals = [];

  if (!empty($document)) {
    foreach ($document as $value) {
      $sucursalId = $value['sucursal_id'];

      if (!isset($documentSucursals[$sucursalId])) {
        $documentSucursals[$sucursalId] = [
          'id' => $value['sucursal_id'],
          'name' => $value['sucursal_name'],
          'items' => []
        ];
      }

      $documentSucursals[$sucursalId]['items'][] = $value;
    }
  }

  $sucursals = [];

  foreach ($invoiceSucursals as $id => $sucursal) {
    $sucursals[$id] = $sucursal;
  }

  foreach ($documentSucursals as $id => $sucursal) {
    if (!isset($sucursals[$id])) {
      $sucursals[$id] = $sucursal;
    }
  }

  ksort($sucursals);
  ?>

  <?php if (!empty($sucursals)): ?>

    <div class="row">

      <?php foreach ($sucursals as $sucursal): ?>

        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-4">

          <div class="card h-100">

            <div class="card-header">
              <h5 class="mb-0">
                <?= esc(
                  str_pad(
                    $sucursal['id'],
                    3,
                    '0',
                    STR_PAD_LEFT
                  )
                  . ' | Secuencias de '
                  . $sucursal['name']
                ) ?>
              </h5>
            </div>

            <div class="card-body">

              <?php
              $invoiceItems =
                $invoiceSucursals[$sucursal['id']]['items'] ?? [];
              ?>

              <div class="card mb-4">

                <div class="card-header">
                  <h6 class="mb-0">
                    FACTURA
                  </h6>
                </div>

                <div class="card-body p-0">

                  <div class="table-responsive">

                    <table class="table table-striped table-hover mb-0">

                      <thead>
                        <tr>
                          <th>
                            PUNTO DE EXPEDICIÓN
                          </th>
                          <th>
                            NÚMERO ACTUAL
                          </th>

                          <?php if (can('sequence_view') or can('sequence_edit')): ?>

                            <?php if (can('sequence_view')): ?>
                              <th scope="col">
                                Ver
                              </th>
                            <?php endif; ?>

                            <?php if (can('sequence_edit')): ?>
                              <th scope="col">
                                Editar
                              </th>
                            <?php endif; ?>

                          <?php endif; ?>
                          <?php if (can('sequence_delete')): ?>
                            <th>
                              Eliminar
                            </th>
                          <?php endif; ?>
                        </tr>
                      </thead>

                      <tbody>

                        <?php if (!empty($invoiceItems)): ?>

                          <?php foreach ($invoiceItems as $value): ?>

                            <tr>

                              <td>
                                <?= esc(
                                  str_pad(
                                    $value['expedition_point_code'],
                                    3,
                                    '0',
                                    STR_PAD_LEFT
                                  )
                                ) ?>
                              </td>

                              <td>
                                <?= esc($value['last_number']) ?>
                              </td>
                              <?php if (can('sequence_view') or can('sequence_edit')): ?>
                                <td>
                                  <button type="button" class="btn btn-outline-warning invoice_sequence_edit_open"
                                    data-id="<?= esc($value['id']) ?>" data-type="invoice">

                                    <?php if (can('sequence_view')): ?>
                                      <i class="fas fa-angle-double-right"></i>
                                    <?php endif; ?>
                                    <?php if (can('sequence_edit')): ?>
                                      <i class="fas fa-pencil-alt"></i>
                                    <?php endif; ?>

                                  </button>
                                </td>
                              <?php endif; ?>
                              <?php if (can('sequence_delete')): ?>
                                <td>

                                  <button type="button" class="btn btn-outline-danger invoice_sequence_delete_open"
                                    data-id="<?= esc($value['id']) ?>" data-name="<?= esc($value['expedition_point_code']) ?>"
                                    data-type="sequence" data-sequence-type="invoice">
                                    <i class="fas fa-trash"></i>
                                  </button>

                                </td>
                              <?php endif; ?>
                            </tr>

                          <?php endforeach; ?>

                        <?php else: ?>

                          <tr>

                            <td colspan="4" class="text-center">
                              NO HAY REGISTROS
                            </td>

                          </tr>

                        <?php endif; ?>

                      </tbody>

                    </table>

                  </div>

                </div>

              </div>

              <?php
              $documentItems =
                $documentSucursals[$sucursal['id']]['items'] ?? [];
              ?>

              <div class="card">

                <div class="card-header">
                  <h6 class="mb-0">
                    INTERNO
                  </h6>
                </div>

                <div class="card-body p-0">

                  <div class="table-responsive">

                    <table class="table table-striped table-hover mb-0">

                      <thead>

                        <tr>

                          <th>
                            PUNTO DE EXPEDICIÓN
                          </th>

                          <th>
                            NÚMERO ACTUAL
                          </th>
                          <?php if (can('sequence_view') or can('sequence_edit')): ?>

                            <?php if (can('sequence_view')): ?>
                              <th scope="col">
                                Ver
                              </th>
                            <?php endif; ?>

                            <?php if (can('sequence_edit')): ?>
                              <th scope="col">
                                Editar
                              </th>
                            <?php endif; ?>

                          <?php endif; ?>
                          <?php if (can('sequence_delete')): ?>
                            <th>
                              Eliminar
                            </th>
                          <?php endif; ?>
                        </tr>

                      </thead>

                      <tbody>

                        <?php if (!empty($documentItems)): ?>

                          <?php foreach ($documentItems as $value): ?>

                            <tr>

                              <td>
                                <?= esc(
                                  str_pad(
                                    $value['expedition_point_code'],
                                    3,
                                    '0',
                                    STR_PAD_LEFT
                                  )
                                ) ?>
                              </td>

                              <td>
                                <?= esc($value['last_number']) ?>
                              </td>

                              <?php if (can('sequence_view') or can('sequence_edit')): ?>

                                <td>

                                  <button type="button" class="btn btn-outline-warning document_sequence_edit_open"
                                    data-id="<?= esc($value['id']) ?>" data-type="document">

                                    <?php if (can('sequence_view')): ?>
                                      <i class="fas fa-angle-double-right"></i>
                                    <?php endif; ?>

                                    <?php if (can('sequence_edit')): ?>
                                      <i class="fas fa-pencil-alt"></i>
                                    <?php endif; ?>

                                  </button>

                                </td>

                              <?php endif; ?>

                              <?php if (can('sequence_delete')): ?>

                                <td>

                                  <button type="button" class="btn btn-outline-danger document_sequence_delete_open"
                                    data-id="<?= esc($value['id']) ?>" data-name="<?= esc($value['expedition_point_code']) ?>"
                                    data-type="sequence" data-sequence-type="document">
                                    <i class="fas fa-trash"></i>
                                  </button>

                                </td>

                              <?php endif; ?>

                            </tr>

                          <?php endforeach; ?>

                        <?php else: ?>

                          <tr>

                            <td colspan="4" class="text-center">
                              NO HAY REGISTROS
                            </td>

                          </tr>

                        <?php endif; ?>

                      </tbody>

                    </table>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      <?php endforeach; ?>

    </div>

  <?php else: ?>

    <div class="card">

      <div class="card-body">

        <div class="text-center py-3">
          NO HAY REGISTROS
        </div>

      </div>

    </div>

  <?php endif; ?>

</div>