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

        <div id="print_panel"> </div> <br>
        <div class="tab-regular">
          <ul class="nav nav-tabs " id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                aria-selected="true"><i class=" fas fa-home"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="picture-tab" data-toggle="tab" href="#picture" role="tab" aria-controls="picture"
                aria-selected="false"><i class="fas fa-image"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                aria-selected="false"><i class="fas fa-tasks"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="contact-tab" data-toggle="tab" href="#movements" role="tab"
                aria-controls="contact" aria-selected="false"><i class=" fas fa-th-list"></i></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="contact-tab" data-toggle="tab" href="#time" role="tab" aria-controls="contact"
                aria-selected="false"><i class=" fas fa-hourglass"></i></a>
            </li>

          </ul>
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
              <div id="box_movement_panel"></div>
              <div id="payment_panel"></div>
              <div id="customer_panel"></div>
              <div id="expedition_point_panel"></div>

            </div>
            <div class="tab-pane fade" id="picture" role="tabpanel" aria-labelledby="picture-tab">
              <div id="history_picture_panel"></div>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
              <div id="history_sales_panel"></div>
            </div>
            <div class="tab-pane fade" id="movements" role="tabpanel" aria-labelledby="contact-tab">
              <div id="history_movements_panel"></div>
            </div>
            <div class="tab-pane fade" id="time" role="tabpanel" aria-labelledby="contact-tab">
              <div id="wait_panel"></div>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>

  <!-- CARD FACTURACION -->
  <div class="col-md-8 mb-8">
    <div class="card">
      <div class="card-header">

        <div id="invoice_panel">

        </div>


        <div id="display_multi_payment"></div>

      </div>
      <div class="card-body">
        <div id="display_cart">
          <div class="table-responsive carrito-scroll">
            <?= $this->include('box/controller/cart'); ?>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>


<?= $this->endSection(); ?>

<?= $this->section('other_head'); ?>

<?= $this->endSection(); ?>

<?= $this->section('other_script'); ?>
<!-- Process -->
<script src="<?= base_url(); ?>/asyng/box/process/sales.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/app.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/cart.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/wait.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/product.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/customer.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/point.js"></script>
<script src="<?= base_url(); ?>/asyng/box/process/invoice_display.js"></script>



<!-- Apps -->
<script src="<?= base_url(); ?>/asyng/box/app/app.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/cart.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/wait.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/product.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/customer.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/point.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app/sales.js"></script>





<!-- <script src="<?php // base_url(); ?>/asyng/box/process.js"></script>
<script src="<?php // base_url(); ?>/asyng/box/send.js"></script>
<script src="<?php // base_url(); ?>/asyng/box/app.js"></script> -->

<?= $this->endSection(); ?>