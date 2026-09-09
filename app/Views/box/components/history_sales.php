<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">

  <div class="section-block">
    <h5 class="section-title">Historial de Ventas</h5>
  </div>

  <div class="tab-regular">

    <ul class="nav nav-tabs nav-fill" id="myTab7" role="tablist">

      <!-- EFECTIVO -->
      <li class="nav-item">
        <a
          class="nav-link active"
          id="cash-tab"
          data-toggle="tab"
          href="#cash"
          role="tab"
          aria-controls="cash"
          aria-selected="true">
          Efectivo
        </a>
      </li>

      <!-- CREDITO -->
      <li class="nav-item">
        <a
          class="nav-link"
          id="credit-tab"
          data-toggle="tab"
          href="#credit"
          role="tab"
          aria-controls="credit"
          aria-selected="false">
          Crédito
        </a>
      </li>

      <!-- OTROS -->
      <li class="nav-item">
        <a
          class="nav-link"
          id="other-tab"
          data-toggle="tab"
          href="#other"
          role="tab"
          aria-controls="other"
          aria-selected="false">
          Otros
        </a>
      </li>

    </ul>


    <div class="tab-content" id="myTabContent7">


      <!-- ===================================================== -->
      <!-- TAB EFECTIVO -->
      <!-- ===================================================== -->

      <div
        class="tab-pane fade show active"
        id="cash"
        role="tabpanel"
        aria-labelledby="cash-tab">

        <div class="table-responsive">

          <table class="table table-hover table-striped table-bordered align-middle">

            <thead>
              <tr>
                <th>HORA</th>
                <th>NUMERO</th>
                <th>CLIENTE</th>
                <th>MONTO</th>
                <th>ENTREGA</th>
                <th>VUELTO</th>
                <th>#</th>
              </tr>
            </thead>

            <tbody>

              <?php if (!empty($cash)): ?>

                <?php foreach ($cash as $row): ?>

                  <tr>

                    <td>
                      <?= esc($row['time'] ?? '') ?>
                    </td>
                    <td>
                      <?= esc($row['sales_number'] ?? '') ?>
                    </td></td>

                    <td>
                      <?= esc($row['customer_name'] ?? 'SIN CLIENTE') ?>
                    </td>

                    <td class="text-end">
                      <?= number_format(
                        (float) ($row['total_price'] ?? 0),
                        0,
                        ',',
                        '.'
                      ) ?>
                    </td>

                 

                    <td class="text-end">
                      <?= number_format(
                        (float) ($row['cash_received'] ?? 0),
                        0,
                        ',',
                        '.'
                      ) ?>
                    </td>

                    <td class="text-end">
                      <?= number_format(
                        (float) ($row['cash_change'] ?? 0),
                        0,
                        ',',
                        '.'
                      ) ?>
                    </td>

                    <td>
                        <button type="button" class="btn btn-outline-primary history_sales_detail"
                        data-id="<?= esc($row['sales'] ?? '') ?>" >
                        <i class=" fas fa-chevron-right"></i>
                      </button>
                      
                    </td>

                  </tr>

                <?php endforeach; ?>

              <?php else: ?>

                <tr>
                  <td colspan="7" class="text-center">
                    NO HAY VENTAS EN EFECTIVO
                  </td>
                </tr>

              <?php endif; ?>

            </tbody>

          </table>

        </div>


        <!-- PAGINACIÓN EFECTIVO -->

        <div class="d-flex justify-content-center align-items-center gap-2 mt-3">

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="history_sales_panel('cash', <?= max(1, $pageCash - 1) ?>)"
            <?= $pageCash <= 1 ? 'disabled' : '' ?>>
            &laquo;
          </button>

          <span class="fw-bold">
            <?= $pageCash ?> / <?= $totalPagesCash ?>
          </span>

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="history_sales_panel('cash', <?= min($totalPagesCash, $pageCash + 1) ?>)"
            <?= $pageCash >= $totalPagesCash ? 'disabled' : '' ?>>
            &raquo;
          </button>

        </div>

      </div>


      <!-- ===================================================== -->
      <!-- TAB CREDITO -->
      <!-- ===================================================== -->

      <div
        class="tab-pane fade"
        id="credit"
        role="tabpanel"
        aria-labelledby="credit-tab">

        <div class="table-responsive">

          <table class="table table-hover table-striped table-bordered align-middle">

            <thead>
              <tr>
                <th>HORA</th>
                <th>NUMERO</th>
                <th>CLIENTE</th>
                <th>MONTO</th>
                <th>#</th>
              </tr>
            </thead>

            <tbody>

              <?php if (!empty($credit)): ?>

                <?php foreach ($credit as $row): ?>

                  <tr>

                    <td>
                      <?= esc($row['time'] ?? '') ?>
                    </td>

                    <td>
                      <?= esc($row['sales_number'] ?? '') ?>
                    </td>

                    <td>
                      <?= esc($row['customer_name'] ?? 'SIN CLIENTE') ?>
                    </td>

                    <td class="text-end">
                      <?= number_format(
                        (float) ($row['total_price'] ?? 0),
                        0,
                        ',',
                        '.'
                      ) ?>
                    </td>

                   <td>
                        <button type="button" class="btn btn-outline-primary history_sales_detail"
                        data-id="<?= esc($row['sales'] ?? '') ?>" >
                        <i class=" fas fa-chevron-right"></i>
                      </button>
                      
                    </td>

                  </tr>

                <?php endforeach; ?>

              <?php else: ?>

                <tr>
                  <td colspan="5" class="text-center">
                    NO HAY VENTAS A CRÉDITO
                  </td>
                </tr>

              <?php endif; ?>

            </tbody>

          </table>

        </div>


        <!-- PAGINACIÓN CRÉDITO -->

        <div class="d-flex justify-content-center align-items-center gap-2 mt-3">

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="history_sales_panel('credit', <?= max(1, $pageCredit - 1) ?>)"
            <?= $pageCredit <= 1 ? 'disabled' : '' ?>>
            &laquo;
          </button>

          <span class="fw-bold">
            <?= $pageCredit ?> / <?= $totalPagesCredit ?>
          </span>

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="history_sales_panel('credit', <?= min($totalPagesCredit, $pageCredit + 1) ?>)"
            <?= $pageCredit >= $totalPagesCredit ? 'disabled' : '' ?>>
            &raquo;
          </button>

        </div>

      </div>


      <!-- ===================================================== -->
      <!-- TAB OTROS -->
      <!-- ===================================================== -->

      <div
        class="tab-pane fade"
        id="other"
        role="tabpanel"
        aria-labelledby="other-tab">

        <div class="table-responsive">

          <table class="table table-hover table-striped table-bordered align-middle">

            <thead>
              <tr>
                <th>HORA</th>
                <th>NUMERO</th>
                <th>CLIENTE</th>
                <th>MONTO</th>
                <th>PAGO</th>
                <th>N.º OPERACIÓN</th>
                <th>DISPOSITIVO</th>
                <th>#</th>
              </tr>
            </thead>

            <tbody>

              <?php if (!empty($other)): ?>

                <?php foreach ($other as $row): ?>

                  <tr>

                    <td>
                      <?= esc($row['time'] ?? '') ?>
                    </td>

                    <td>
                      <?= esc($row['sales_number'] ?? '') ?>
                    </td>

                    <td>
                      <?= esc($row['customer_name'] ?? 'SIN CLIENTE') ?>
                    </td>

                    <td class="text-end">
                      <?= number_format(
                        (float) ($row['total_price'] ?? 0),
                        0,
                        ',',
                        '.'
                      ) ?>
                    </td>

                    <td>
                      <?= esc($row['payment_name'] ?? '') ?>
                    </td>

                    <td>
                      <?= esc($row['operation_number'] ?? '') ?>
                    </td>

                    <td>
                      <?= esc($row['device_name'] ?? '') ?>
                    </td>

                    <td>
                        <button type="button" class="btn btn-outline-primary history_sales_detail"
                        data-id="<?= esc($row['sales'] ?? '') ?>" >
                        <i class=" fas fa-chevron-right"></i>
                      </button>
                      
                    </td>

                  </tr>

                <?php endforeach; ?>

              <?php else: ?>

                <tr>
                  <td colspan="8" class="text-center">
                    NO HAY OTRAS VENTAS
                  </td>
                </tr>

              <?php endif; ?>

            </tbody>

          </table>

        </div>


        <!-- PAGINACIÓN OTROS -->

        <div class="d-flex justify-content-center align-items-center gap-2 mt-3">

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="history_sales_panel('other', <?= max(1, $pageOther - 1) ?>)"
            <?= $pageOther <= 1 ? 'disabled' : '' ?>>
            &laquo;
          </button>

          <span class="fw-bold">
            <?= $pageOther ?> / <?= $totalPagesOther ?>
          </span>

          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            onclick="history_sales_panel('other', <?= min($totalPagesOther, $pageOther + 1) ?>)"
            <?= $pageOther >= $totalPagesOther ? 'disabled' : '' ?>>
            &raquo;
          </button>

        </div>

      </div>


    </div>

  </div>

</div>