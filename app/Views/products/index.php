<?= $this->extend('interface/interface'); ?>


<?= $this->section('main'); ?>
<div class="row">
  <div class="col-md-12 mb-3">
    <div id="global-alert-container"></div>
  </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">

  <div class="tab-vertical">

    <ul class="nav nav-tabs" id="myTab3" role="tablist">

      <!-- PRODUCTOS -->
      <li class="nav-item">
        <a class="nav-link active" id="home-vertical-tab" data-toggle="tab" href="#home-vertical" role="tab"
          aria-controls="home-vertical" aria-selected="true">
          Productos
        </a>
      </li>

      <!-- PROVEEDORES -->
      <li class="nav-item">
        <a class="nav-link" id="profile-vertical-tab" data-toggle="tab" href="#profile-vertical" role="tab"
          aria-controls="profile-vertical" aria-selected="false">
          Proveedores
        </a>
      </li>

      <!-- TRANSFERENCIAS -->
      <li class="nav-item">
        <a class="nav-link" id="contact-vertical-tab" data-toggle="tab" href="#contact-vertical" role="tab"
          aria-controls="contact-vertical" aria-selected="false">
          Transferencias
        </a>
      </li>

    </ul>

    <div class="tab-content" id="myTabContent3">

      <!-- PANEL PRODUCTOS -->
      <div class="tab-pane fade show active" id="home-vertical" role="tabpanel" aria-labelledby="home-vertical-tab">

        <div id="products_panel"></div>

      </div>

      <!-- PANEL PROVEEDORES -->
      <div class="tab-pane fade" id="profile-vertical" role="tabpanel" aria-labelledby="profile-vertical-tab">

        <div id="provider_panel"></div>

      </div>

      <!-- PANEL TRANSFERENCIAS -->
      <div class="tab-pane fade" id="contact-vertical" role="tabpanel" aria-labelledby="contact-vertical-tab">

        <div id="transfers_panel"></div>

      </div>

    </div>

  </div>

</div>
<?= $this->endSection(); ?>


<!-- Other Head -->
<?= $this->section('other_head'); ?>
<?= $this->include('interface/other/head/datatables'); ?>
<!-- QZ TRAY IMPRESION -->
<script src=" <?= base_url(); ?>/assets/libs/js/qz-tray.js"></script>
<script src=" <?= base_url(); ?>/assets/libs/js/JsBarcode.all.min.js"></script>

<!-- ----------------- -->
<?= $this->endSection(); ?>

<!-- Other Script -->
<?= $this->section('other_script'); ?>

<?= $this->include('interface/other/scripts/datatables'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>
<script src="<?= base_url(); ?>/asyng/products/products/process.js"></script>
<!-- IMPRESION -->
<script src="<?= base_url(); ?>/asyng/prints/barcode.js"></script>
<script src="<?= base_url(); ?>/asyng/prints/name.js"></script>
<script src="<?= base_url(); ?>/asyng/prints/label.js"></script>

<script src="<?= base_url(); ?>/asyng/prints/ticket.js"></script>
<script src="<?= base_url(); ?>/asyng/prints/invoice.js"></script>
<!-- --------- -->

<!-- NUEVO SCRIPT -->

<!-- Products -->
<!-- Form -->
<script src="<?= base_url(); ?>/asyng/products/products/form/product_crud.js"></script>
<script src="<?= base_url(); ?>/asyng/products/products/form/product_process.js"></script>

<script src="<?= base_url(); ?>/asyng/products/products/form/product.js"></script>
<!-- Brand -->
<script src="<?= base_url(); ?>/asyng/products/brand/process.js"></script>
<script src="<?= base_url(); ?>/asyng/products/brand/domm.js"></script>
<!-- Section -->
<script src="<?= base_url(); ?>/asyng/products/section/process.js"></script>
<script src="<?= base_url(); ?>/asyng/products/section/domm.js"></script>

<!-- Provider -->
<script src="<?= base_url(); ?>/asyng/products/provider/process.js"></script>
<script src="<?= base_url(); ?>/asyng/products/provider/domm.js"></script>


<!-- Shopp -->
<script src="<?= base_url(); ?>/asyng/products/products/shopp/shopp_save.js"></script>

<script src="<?= base_url(); ?>/asyng/products/products/shopp/shopp_win.js"></script>
<script src="<?= base_url(); ?>/asyng/products/products/shopp/shopp.js"></script>

<!-- home -->
<script src="<?= base_url(); ?>/asyng/products/products/home/print.js"></script>
<script src="<?= base_url(); ?>/asyng/products/products/home/view.js"></script>
<script src="<?= base_url(); ?>/asyng/products/products/home/home.js"></script>


<script src="<?= base_url(); ?>/asyng/products/tools.js"></script>

<script src="<?= base_url(); ?>/asyng/products/app.js"></script>



<?= $this->endSection(); ?>