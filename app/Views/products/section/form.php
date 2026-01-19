<div class="row justify-content-center mt-4">
  <div class="col-xl-6 col-lg-7 col-md-8 col-sm-12">

    <div class="card shadow-sm border-0">

      <!-- HEADER -->
      <div class="card-header bg-light text-center border-0">
        <h4 id="brand_title" class="mb-0 font-weight-bold">
          <?= esc($title) ?>
        </h4>
      </div>

      <!-- BODY -->
      <div class="card-body px-4 py-4">
        <form action="" id="section_form">

          <input type="text" class="form-control" name="id" hidden>

          <div class="form-group text-center">
            <label class="font-weight-bold mb-2">
              Descripción de la sección
            </label>

            <div class="input-group input-group-lg">
              <input type="text" class="form-control text-center col-xl-12" name="name" autofocus
                placeholder="Ingrese la descripción">

              <div class="input-group-append">
                <button type="button" class="btn btn-primary px-4 section_save" title="Guardar">
                  <i class="fas fa-check"></i>
                </button>

                <button type="button" class="btn btn-outline-danger px-4 section_cancel" title="Cancelar">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>

        </form>
      </div>

      <!-- FOOTER -->
      <div class="card-footer bg-white border-0 text-muted text-center small">
        Complete la descripción y presione guardar
      </div>

    </div>

  </div>
</div>