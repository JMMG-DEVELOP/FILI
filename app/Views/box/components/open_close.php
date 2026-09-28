<?= $this->extend('interface/interface'); ?>
<?= $this->section('main'); ?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card" id="switchcontent">
      <h5 class="card-header"> Control de Efectivo </h5>
      <div class="card-body" id="display_close">
        <form id="form_cash_close">
          <!-- ========================================== BILLETES Y MONEDAS =========================================== -->
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th style="width: 180px;">Denominación</th>
                  <th style="width: 220px;">Billete / Moneda</th>
                  <th style="width: 180px;">Cantidad</th>
                  <th style="width: 200px;">Total</th>
                </tr>
              </thead>
              <tbody> <!-- 100.000 -->
                <tr>
                  <td> <strong>₲ 100.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/100000.jpg') ?>"
                      alt="Billete 100.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_100000" id="cant_100000"
                      min="0" step="1" value="0" data-value="100000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 50.000 -->
                <tr>
                  <td> <strong>₲ 50.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/50000.jpg') ?>"
                      alt="Billete 50.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_50000" id="cant_50000" min="0"
                      step="1" value="0" data-value="50000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 20.000 -->
                <tr>
                  <td> <strong>₲ 20.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/20000.jpg') ?>"
                      alt="Billete 20.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_20000" id="cant_20000" min="0"
                      step="1" value="0" data-value="20000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 10.000 -->
                <tr>
                  <td> <strong>₲ 10.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/10000.jpg') ?>"
                      alt="Billete 10.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_10000" id="cant_10000" min="0"
                      step="1" value="0" data-value="10000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 5.000 -->
                <tr>
                  <td> <strong>₲ 5.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/5000.jpg') ?>"
                      alt="Billete 5.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_5000" id="cant_5000" min="0"
                      step="1" value="0" data-value="5000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 2.000 -->
                <tr>
                  <td> <strong>₲ 2.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/2000.jpg') ?>"
                      alt="Billete 2.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_2000" id="cant_2000" min="0"
                      step="1" value="0" data-value="2000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 1.000 -->
                <tr>
                  <td> <strong>₲ 1.000</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/1000.jpg') ?>"
                      alt="Billete 1.000" class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_1000" id="cant_1000" min="0"
                      step="1" value="0" data-value="1000"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 500 -->
                <tr>
                  <td> <strong>₲ 500</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/500.jpg') ?>" alt="Moneda 500"
                      class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_500" id="cant_500" min="0"
                      step="1" value="0" data-value="500"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 100 -->
                <tr>
                  <td> <strong>₲ 100</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/100.jpg') ?>" alt="Moneda 100"
                      class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_100" id="cant_100" min="0"
                      step="1" value="0" data-value="100"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr> <!-- 50 -->
                <tr>
                  <td> <strong>₲ 50</strong> </td>
                  <td class="text-center"> <img src="<?= base_url('assets/img/billetes/50.jpg') ?>" alt="Moneda 50"
                      class="cash-bill-image"> </td>
                  <td> <input type="number" class="form-control cash-quantity" name="cant_50" id="cant_50" min="0"
                      step="1" value="0" data-value="50"> </td>
                  <td class="text-right"> <strong class="cash-total"> ₲ 0 </strong> </td>
                </tr>

              </tbody>
              <!-- ========================================== TOTAL GENERAL =========================================== -->
              <tfoot>
                <tr>
                  <td colspan="3" class="text-right"> <strong style="font-size: 18px;"> TOTAL EFECTIVO </strong> </td>
                  <td class="text-right"> <strong id="cash_grand_total" style="font-size: 20px;"> ₲ 0 </strong> </td>
                </tr>
                <?php
                if ($type === 'close') { ?>

                  <tr>
                    <td colspan="3" class="text-right"> <strong style="font-size: 18px;"> TOTAL QR </strong> </td>
                    <td class="text-right">
                      <input type="text" id="qr_grand_total" class="form-control money">
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-right"> <strong style="font-size: 18px;"> TOTAL TRANSFERENCIA</strong>
                    </td>
                    <td class="text-right">
                      <input type="text" id="transferencia_grand_total" class="form-control money">
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-right"> <strong style="font-size: 18px;"> TOTAL TARJETA</strong>
                    </td>
                    <td class="text-right">
                      <input type="text" id="tarjeta_grand_total" class="form-control money">
                    </td>
                  </tr>

                <?php } ?>


              </tfoot>
            </table>
          </div>
          <!-- ========================================== BOTÓNES =========================================== -->
          <?php
          if ($type === 'close') {

            ?>
            <div class="row mt-4">
              <div class="col-12 text-right"> <button type="button" id="btn_box_close" class="btn btn-primary"> <i
                    class="fas fa-cash-register"></i> CIERRE DE CAJA </button> </div>
            </div>
            <?php
          }
          ?>
          <?php
          if ($type === 'open') {

            ?>
            <div class="row mt-4">
              <div class="col-12 text-right"> <button type="button" id="btn_box_open" class="btn btn-primary"> <i
                    class="fas fa-cash-register"></i> APERTURA DE CAJA </button> </div>
            </div>
            <?php
          }
          ?>

        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('other_head'); ?>
<style>
  .cash-bill-image {
    width: 160px;
    height: 65px;
    object-fit: contain;
    border-radius: 4px;
  }

  .cash-quantity {
    max-width: 160px;
    margin: auto;
  }

  .cash-total {
    font-size: 16px;
  }
</style>
<?= $this->endSection(); ?>

<?= $this->section('other_script'); ?>
<script src="<?= base_url(); ?>/asyng/box/process/open_close.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/open_close.js"></script>

<?= $this->endSection(); ?>