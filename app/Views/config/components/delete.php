<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Eliminar</h5>

      <a href="#" class="btn btn-outline-danger delete_close"> <i class="fas fa-times"></i>
      </a>

    </div>
    <div class="card-body">
      <h5>Seguro que desea eliminar?</h5>
      <h1>
        <?= $name ?>
      </h1>
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center">


      <?php if ($type === 'printer') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_printer" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
    </div>
  </div>
</div>