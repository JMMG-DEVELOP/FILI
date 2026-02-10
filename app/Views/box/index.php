<?= $this->extend('interface/interface'); ?>
<?= $this->section('main'); ?>
<div class="row">
  <div class="col-md-12 mb-12 justify-content-center">
    <div id="global-alert-container"></div>
  </div>
</div>
<div class="row">

  <div class="col-md-6 mb-6">
    <div class="card">
      <div class="card-header">

        <?= $this->include('interface/components/search'); ?>
        <div id="search_panel"></div>
        <div id="money_panel"></div>
        <div id="invoice_panel"></div>
        <div id="product_panel"></div>

      </div>
      <div class="card-body">

      </div>
    </div>
  </div>

  <div class="col-md-6 mb-6">
    <div class="card">
      <div class="card-header">
        <h4 class="d-flex justify-content-between align-items-center mb-0">
          <span class="text-muted">Facturas</span>
          <span class="badge badge-secondary badge-pill" id="row_invoice">12</span>
        </h4>
      </div>
      <div class="card-body">



      </div>
    </div>
  </div>

</div>


<?= $this->endSection(); ?>

<?= $this->section('other_head'); ?>

<?= $this->endSection(); ?>

<?= $this->section('other_script'); ?>

<script src="<?= base_url(); ?>/asyng/box/process.js"></script>
<script src="<?= base_url(); ?>/asyng/box/app.js"></script>
<script src="<?= base_url(); ?>/asyng/box/send.js"></script>

<?= $this->endSection(); ?>