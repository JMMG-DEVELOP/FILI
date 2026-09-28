<?= $this->extend('interface/interface'); ?>


<?= $this->section('main'); ?>

<div class="row">
  <div class="col-md-12 mb-12 justify-content-center">
    <div id="global-alert-container"></div>
  </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">
  <div class="section-block">
    <h5 class="section-title">CONFIGURACIÓN</h5>
  </div>
  <div class="tab-vertical">
    <ul class="nav nav-tabs" id="myTab3" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="home-vertical-tab" data-toggle="tab" href="#home-vertical" role="tab"
          aria-controls="home" aria-selected="true"> <i class="fas fa-home"></i> </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="profile-vertical-tab" data-toggle="tab" href="#profile-vertical" role="tab"
          aria-controls="profile" aria-selected="false"> <i class="fas fa-print"></i> </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="contact-vertical-tab" data-toggle="tab" href="#contact-vertical" role="tab"
          aria-controls="contact" aria-selected="false">Tab Vertical #3</a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent3">
      <div class="tab-pane fade show active" id="home-vertical" role="tabpanel" aria-labelledby="home-vertical-tab">

        <div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
          <div class="card">
            <div class="card-body">
              <h3 class="card-title border-bottom pb-2">Datos de Usuario</h3>

              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">ID</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left"> <?= $user_id ?> </label>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">USUARIO</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left">
                    <?= $user_user ?>
                  </label>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">NOMBRE Y APELLIDO</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left">
                    <?= $user_name ?>
                  </label>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">CATEGORIA</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left">
                    <?= $category_name ?>
                  </label>
                </div>
              </div>


            </div>
          </div>
        </div>

        <div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
          <div class="card">
            <div class="card-body">
              <h3 class="card-title border-bottom pb-2">Datos de Sesión</h3>

              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">DISPOSITIVO</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left">
                    <?= $session['device_type'] ?>
                  </label>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">UUID</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left">
                    <?= $session['device_uuid'] ?>
                  </label>
                </div>
              </div>
              <div class="form-group row">
                <label class="col-12 col-sm-3 col-form-label text-sm-right">NAVEGADOR</label>
                <div class="col-12 col-sm-8 col-lg-6 ">
                  <label class="col-12 col-sm-3 col-form-label text-sm-left">
                    <?= $session['session_browser'] ?>
                  </label>
                </div>
              </div>

            </div>
          </div>
        </div>


      </div>
      <div class="tab-pane fade" id="profile-vertical" role="tabpanel" aria-labelledby="profile-vertical-tab">

        <?= $this->include('config/panels/printers'); ?>


      </div>

      <div class="tab-pane fade" id="contact-vertical" role="tabpanel" aria-labelledby="contact-vertical-tab">

      </div>
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

<!-- process -->
<script src="<?= base_url(); ?>/asyng/config/process/printers/printers.js"></script>


<!-- app -->
<script src="<?= base_url(); ?>/asyng/config/app/printers/printers.js"></script>

<script src="<?= base_url(); ?>/asyng/config/app/app.js"></script>



<?= $this->endSection(); ?>