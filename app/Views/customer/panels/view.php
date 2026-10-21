<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-lg-12 col-md-12 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-start">

      <div class="flex-grow-1">

        <h3 class="mb-3">
          <i class="fas fa-user text-primary mr-2"></i>
          <?= esc($values['name'] ?? 'CLIENTE') ?>
        </h3>

        <div class="row">

          <input type="hidden" id="id_cliente" value="<?= esc($values['id'] ?? '-') ?>">
          <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Cédula / RUC
            </small>

            <strong>
              <?= esc($values['ci'] ?? '-') ?>
            </strong>
          </div>

          <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Celular
            </small>

            <strong>
              <?= esc($values['cel'] ?? '-') ?>
            </strong>
          </div>

          <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Correo electrónico
            </small>

            <strong>
              <?= esc($values['correo'] ?? '-') ?>
            </strong>
          </div>

          <div class="col-xl-3 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Crédito
            </small>

            <?php if ((int) ($values['credit_status'] ?? 0) === 1): ?>
              <span class="badge badge-success">
                Habilitado
              </span>
            <?php else: ?>
              <span class="badge badge-secondary">
                Deshabilitado
              </span>
            <?php endif; ?>
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Creado por
            </small>

            <strong>
              <?= esc($values['created_by'] ?? 'Usuario no disponible') ?>
            </strong>
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Fecha de creación
            </small>

            <strong>
              <?= esc($values['date'] ?? '-') ?>
            </strong>
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 mb-2">
            <small class="text-muted d-block">
              Hora de creación
            </small>

            <strong>
              <?= esc($values['time'] ?? '-') ?>
            </strong>
          </div>

        </div>
      </div>

      <a href="#" class="btn btn-outline-danger ml-3" id="customer_view_close" title="Cerrar">
        <i class="fas fa-times"></i>
      </a>

    </div>

    <div class="tab-regular">
      <ul class="nav nav-tabs " id="myTab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
            aria-selected="true"><i class="fas fa-home"></i></a>
        </li>
        <?php if (can('customer_sales_view')): ?>
          <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
              aria-selected="false"><i class="fas fa-asterisk"></i></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="anoter-tab" data-toggle="tab" href="#anoter" role="tab" aria-controls="anoter"
              aria-selected="false"><i class=" fas fa-pencil-alt"></i></a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link" id="devices-tab" data-toggle="tab" href="#devices" role="tab" aria-controls="devices"
            aria-selected="false"><i class="fas fa-handshake"></i></a>
        </li>


      </ul>
      <div class="tab-content" id="myTabContent">

        <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div id="panel_view_home"></div>
          <div id="panel_view_form"></div>
        </div>

        <?php if (can('customer_sales_view')): ?>
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div id="panel_sales">

            </div>
          </div>
          <div class="tab-pane fade" id="anoter" role="tabpanel" aria-labelledby="anoter-tab">
            <div id="panel_anoter">

            </div>
          </div>
        <?php endif; ?>



        <div class="tab-pane fade" id="devices" role="tabpanel" aria-labelledby="devices-tab">
          <div id="panel_payment">
          </div>
        </div>



      </div>
    </div>



  </div>
</div>