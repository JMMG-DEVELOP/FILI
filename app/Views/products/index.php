<?= $this->extend('interface/interface'); ?>


<?= $this->section('main'); ?>
<!-- Widget -->
<div id="products_widget"></div>
<!-- End Widget -->

<?php
$activeTab = null;

if (can('product_products_view')) {
  $activeTab = 'products';
} elseif (can('product_brands_view')) {
  $activeTab = 'brands';
} elseif (can(permissions: 'product_sections_view')) {
  $activeTab = 'sections';
}
?>

<!-- <div id="globalFormAlert" class="alert alert-warning alert-dismissible fade" role="alert">
  <strong>Atención!</strong>
  <span class="alert-message"></span>
  <a href="#" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
  </a>
</div> -->


<!-- Panel Sidebar -->
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">
  <div class="tab-vertical">

    <ul class="nav nav-tabs" id="myTab3" role="tablist">
      <?php if (can('product_products_view')): ?>
        <li class="nav-item">
          <a class="nav-link active show" id="home-vertical-tab" data-toggle="tab" href="#home-vertical" role="tab"
            aria-selected="true">
            Productos
          </a>
        </li>
      <?php endif; ?>

      <?php if (can('product_brands_view')): ?>
        <li class="nav-item">
          <a class="nav-link" id="profile-vertical-tab" data-toggle="tab" href="#profile-vertical" role="tab"
            aria-selected="false">
            Marcas
          </a>
        </li>
      <?php endif; ?>

      <?php if (can('product_sections_view')): ?>
        <li class="nav-item">
          <a class="nav-link" id="contact-vertical-tab" data-toggle="tab" href="#contact-vertical" role="tab"
            aria-selected="false">
            Secciones
          </a>
        </li>
      <?php endif; ?>
    </ul>

    <div class="tab-content" id="myTabContent3">

      <!-- PRODUCTOS -->

      <div class="tab-pane fade <?= $activeTab === 'products' ? 'active show' : '' ?>" id="home-vertical"
        role="tabpanel" aria-labelledby="home-vertical-tab">
        <div id="products_panel">
          <?= $this->include('products/products/datatable'); ?>
        </div>
        <div id="products_form"></div>
      </div>

      <!-- MARCAS -->

      <div class="tab-pane fade <?= $activeTab === 'brands' ? 'active show' : '' ?>" id="profile-vertical"
        role="tabpanel" aria-labelledby="profile-vertical-tab">

        <div id="brands_panel">
          <?= $this->include('products/brands/datatable'); ?>
        </div>

        <div id="brands_form"></div>
      </div>

      <!-- SECCIONES -->

      <div class="tab-pane fade <?= $activeTab === 'sections' ? 'active show' : '' ?>" id="contact-vertical"
        role="tabpanel" aria-labelledby="contact-vertical-tab">

        <div id="section_panel">
          <?= $this->include('products/section/datatable'); ?>
        </div>

        <div id="section_form"></div>
      </div>

    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<!-- Other Head -->
<?= $this->section('other_head'); ?>
<?= $this->include('interface/other/head/datatables'); ?>
<?= $this->endSection(); ?>

<!-- Other Script -->
<?= $this->section('other_script'); ?>
<?= $this->include('interface/other/scripts/datatables'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"></script>


<script src="<?= base_url(); ?>/asyng/products/products/app.js"></script>
<script src="<?= base_url(); ?>/asyng/products/brands/app.js"></script>
<script src="<?= base_url(); ?>/asyng/products/section/app.js"></script>


<script src="<?= base_url(); ?>/asyng/initDatatables.js"></script>

<?= $this->endSection(); ?>