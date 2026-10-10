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
          aria-controls="contact" aria-selected="false"><i class="fas fa-user"></i> </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="point-vertical-tab" data-toggle="tab" href="#point-vertical" role="tab"
          aria-controls="point" aria-selected="false"><i class="fas fa-square"></i> </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="sucursals-vertical-tab" data-toggle="tab" href="#sucursals-vertical" role="tab"
          aria-controls="sucursals" aria-selected="false"><i class="fas fa-warehouse"></i> </a>
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
        <?= $this->include('config/panels/users'); ?>
      </div>
      <div class="tab-pane fade" id="point-vertical" role="tabpanel" aria-labelledby="contact-vertical-tab">
        <?= $this->include('config/panels/points'); ?>
      </div>
      <div class="tab-pane fade" id="sucursals-vertical" role="tabpanel" aria-labelledby="sucursals-vertical-tab">
        <?= $this->include('config/panels/sucursals'); ?>
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
<script src=" <?= base_url(); ?>/asyng/config/process/printers/drivers.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/printers/papers.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/printers/devices.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/printers/asignation.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/users/category.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/users/users.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/users/asignation.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/points/point.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/points/sequence.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/points/asignation.js"></script>
<script src="<?= base_url(); ?>/asyng/config/process/sucursals/sucursal.js"></script>

<!-- app -->
<script src="<?= base_url(); ?>/asyng/config/app/printers/printers.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/printers/drivers.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/printers/papers.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/printers/devices.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/printers/asignation.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/users/category.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/users/users.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/users/asignation.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/points/point.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/points/sequence.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/points/asignation.js"></script>
<script src="<?= base_url(); ?>/asyng/config/app/sucursals/sucursal.js"></script>


<script src="<?= base_url(); ?>/asyng/config/app/app.js"></script>

<?= $this->endSection(); ?>