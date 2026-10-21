<?= $this->extend('interface/interface'); ?>


<?= $this->section('main'); ?>
<div class="row">
  <div class="col-md-12 mb-12 justify-content-center">
    <div id="global-alert-container"></div>
  </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">

  <div class="tab-vertical">
    <ul class="nav nav-tabs" id="myTab3" role="tablist">

      <li class="nav-item">
        <a class="nav-link active" id="home-vertical-tab" data-toggle="tab" href="#home-vertical" role="tab"
          aria-controls="home" aria-selected="true">
          <i class="fas fa-home"></i>
        </a>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link" id="profile-vertical-tab" data-toggle="tab" href="#profile-vertical" role="tab"
          aria-controls="profile" aria-selected="false">Tab Vertical #2</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" id="contact-vertical-tab" data-toggle="tab" href="#contact-vertical" role="tab"
          aria-controls="contact" aria-selected="false">Tab Vertical #3</a>
      </li> -->

    </ul>

    <div class="tab-content" id="myTabContent3">
      <div class="tab-pane fade show active" id="home-vertical" role="tabpanel" aria-labelledby="home-vertical-tab">
        <div id="panel_home"></div>
      </div>

      <!-- <div class="tab-pane fade" id="profile-vertical" role="tabpanel" aria-labelledby="profile-vertical-tab">

      </div>

      <div class="tab-pane fade" id="contact-vertical" role="tabpanel" aria-labelledby="contact-vertical-tab">

      </div> -->

    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<!-- Other Head -->
<?= $this->section('other_head'); ?>
<?= $this->include('interface/other/head/datatables'); ?>

<?= $this->endSection(); ?>

<!-- Other script -->

<?= $this->section('other_script'); ?>
<?= $this->include('interface/other/scripts/datatables'); ?>

<script src="<?= base_url(); ?>/asyng/customer/home.js"></script>
<script src="<?= base_url(); ?>/asyng/customer/detail.js"></script>
<script src="<?= base_url(); ?>/asyng/customer/sales.js"></script>
<script src="<?= base_url(); ?>/asyng/customer/payment.js"></script>
<script src="<?= base_url(); ?>/asyng/customer/view.js"></script>

<script src="<?= base_url(); ?>/asyng/customer/app.js"></script>



<?= $this->endSection(); ?>