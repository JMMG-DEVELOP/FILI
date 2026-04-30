<?= $this->extend('interface/interface'); ?>
<?= $this->section('main'); ?>
<div class="row">
  <div class="col-md-12 mb-12 justify-content-center">
    <div id="global-alert-container"></div>
  </div>
</div>
<div class="row">
  <!-- CONTROLLER FACTURACION -->
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-header bg-light">
        <div id="controller_panel"></div>
      </div>

      <div class="card-body">
        <div id="payment_panel">
        </div>
        <div id="customer_panel"></div>

        <div id="expedition_point_panel">
          <?php // $this->include('box/controller/expedition_point'); ?>
        </div>
      </div>

    </div>
  </div>

  <!-- CARD FACTURACION -->
  <div class="col-md-8 mb-8">
    <div class="card">
      <div class="card-header">

        <?= $this->include('box/components/total_cart'); ?>

        <div id="display_procedures">


        </div>

      </div>
      <div class="card-body">
        <div id="display_cart">
          <?= $this->include('box/controller/cart'); ?>
        </div>
      </div>
    </div>
  </div>

</div>


<?= $this->endSection(); ?>

<?= $this->section('other_head'); ?>

<?= $this->endSection(); ?>

<?= $this->section('other_script'); ?>
<script src="<?= base_url(); ?>/asyng/js/soundManager.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>
<!-- Process -->
<script src="<?= base_url(); ?>/asyng/box/process/sales.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/app.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/cart.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/product.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/customer.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/point.js"></script>



<!-- Apps -->
<script src="<?= base_url(); ?>/asyng/box/app/app.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/cart.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/product.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/customer.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/point.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/sales.js"></script>




<!-- <script src="<?php // base_url(); ?>/asyng/box/process.js"></script>
<script src="<?php // base_url(); ?>/asyng/box/send.js"></script>
<script src="<?php // base_url(); ?>/asyng/box/app.js"></script> -->

<?= $this->endSection(); ?>