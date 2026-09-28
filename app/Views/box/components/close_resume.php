<?php

$closing = $values['closing'] ?? [];

$formatMoney = function ($value) {

  return '₲ ' . number_format(
    (float) $value,
    0,
    ',',
    '.'
  );

};

?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

  <div class="card" id="switchcontent">

    <h5 class="card-header">
      Resumen de Caja
    </h5>

    <div class="card-body">

      <form id="form_cash_close">

        <div class="table-responsive">

          <table class="table table-bordered table-hover">

            <thead>

              <tr>

                <th style="width: 180px;">
                  TIPO DE PAGO
                </th>

                <th style="width: 220px;">
                  MONTO SISTEMA
                </th>

                <th style="width: 180px;">
                  MONTO REPORTADO
                </th>

                <th style="width: 200px;">
                  DIFERENCIA
                </th>

              </tr>

            </thead>


            <tbody>


              <!-- ===================================================== -->
              <!-- EFECTIVO -->
              <!-- ===================================================== -->

              <tr>

                <td>
                  <strong>
                    EFECTIVO
                  </strong>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['cash']['system'] ?? 0
                  ) ?>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['cash']['close'] ?? 0
                  ) ?>
                </td>

                <td class="text-right">

                  <strong>
                    <?= $formatMoney(
                      $closing['cash']['difference'] ?? 0
                    ) ?>
                  </strong>

                </td>

              </tr>


              <!-- ===================================================== -->
              <!-- QR -->
              <!-- ===================================================== -->

              <tr>

                <td>
                  <strong>
                    QR
                  </strong>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['qr']['system'] ?? 0
                  ) ?>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['qr']['close'] ?? 0
                  ) ?>
                </td>

                <td class="text-right">

                  <strong>
                    <?= $formatMoney(
                      $closing['qr']['difference'] ?? 0
                    ) ?>
                  </strong>

                </td>

              </tr>


              <!-- ===================================================== -->
              <!-- TRANSFERENCIA -->
              <!-- ===================================================== -->

              <tr>

                <td>
                  <strong>
                    TRANSFERENCIAS
                  </strong>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['transfer']['system'] ?? 0
                  ) ?>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['transfer']['close'] ?? 0
                  ) ?>
                </td>

                <td class="text-right">

                  <strong>
                    <?= $formatMoney(
                      $closing['transfer']['difference'] ?? 0
                    ) ?>
                  </strong>

                </td>

              </tr>


              <!-- ===================================================== -->
              <!-- TARJETA -->
              <!-- ===================================================== -->

              <tr>

                <td>
                  <strong>
                    TARJETA
                  </strong>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['card']['system'] ?? 0
                  ) ?>
                </td>

                <td>
                  <?= $formatMoney(
                    $closing['card']['close'] ?? 0
                  ) ?>
                </td>

                <td class="text-right">

                  <strong>
                    <?= $formatMoney(
                      $closing['card']['difference'] ?? 0
                    ) ?>
                  </strong>

                </td>

              </tr>




            </tbody>

          </table>

        </div>


        <!-- ===================================================== -->
        <!-- BOTÓN FINAL -->
        <!-- ===================================================== -->

        <div class="row mt-4">

          <div class="col-12 text-right">

            <a href=" <?= base_url(); ?>" class="btn btn-primary"> CERRAR </a>

          </div>

        </div>


      </form>

    </div>

  </div>

</div>