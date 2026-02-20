<?= $this->extend('interface/interface'); ?>
<?= $this->section('main'); ?>
<div class="row">
  <div class="col-md-12 mb-12 justify-content-center">
    <div id="global-alert-container"></div>
  </div>
</div>
<div class="row">
  <!-- CONTROLLER FACTURACION -->
  <div class="col-md-5 mb-5">
    <div class="card">
      <div class="card-header bg-light">
        <?= $this->include('box/controller/function'); ?>

        <?= $this->include('interface/components/search'); ?>
        <div id="search_panel"></div>

        <div id="product_panel">
          <?= $this->include('box/controller/product'); ?>
        </div>

      </div>
      <div class="card-body">
        <div id="sucursal_panel">
          <?= $this->include('box/controller/sucursal'); ?>
        </div>
        <div id="customer_panel">
          <?= $this->include('box/controller/customer'); ?>
        </div>
        <div id="payment_panel">
          <?= $this->include('box/controller/payment'); ?>
        </div>
        <div id="product_panel"></div>
      </div>
    </div>
  </div>

  <!-- CARD FACTURACION -->
  <div class="col-md-7 mb-7">
    <div class="card">
      <div class="card-header">

        <?= $this->include('box/components/total_cart'); ?>


      </div>
      <div class="card-body">

        <?= $this->include('box/controller/cart'); ?>

      </div>
    </div>
  </div>

</div>


<?= $this->endSection(); ?>

<?= $this->section('other_head'); ?>

<?= $this->endSection(); ?>

<?= $this->section('other_script'); ?>
<script src="<?= base_url(); ?>/asyng/js/soundManager.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app.js"></script>
<script src="<?= base_url(); ?>/asyng/box/send.js"></script>

<?= $this->endSection(); ?>