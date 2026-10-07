<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <?php $sequenceType = $sequence_type ?? ''; ?>
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Eliminar</h5>
      <?php if ($type === 'printer') { ?>
        <a href="#" class="btn btn-outline-danger delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'driver') { ?>
        <a href="#" class="btn btn-outline-danger driver_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'paper') { ?>
        <a href="#" class="btn btn-outline-danger paper_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'category') { ?>
        <a href="#" class="btn btn-outline-danger category_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'device') { ?>
        <a href="#" class="btn btn-outline-danger devices_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'asignation') { ?>
        <a href="#" class="btn btn-outline-danger asignation_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'user') { ?>
        <a href="#" class="btn btn-outline-danger users_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'point') { ?>
        <a href="#" class="btn btn-outline-danger point_point_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
      <?php if ($type === 'sequence') { ?>
        <a href="#" class="btn btn-outline-danger sequence_delete_close"> <i class="fas fa-times"></i>
        </a>
      <?php } ?>
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
      <?php if ($type === 'driver') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_driver" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
      <?php if ($type === 'paper') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_paper" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>

      <?php if ($type === 'category') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_category" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
      <?php if ($type === 'user') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_users" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
      <?php if ($type === 'device') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_devices" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
      <?php if ($type === 'asignation') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_asignation" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
      <?php if ($type === 'point') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_point" data-id="<?= $id ?>">
          <i class="fas fa-trash"></i>
        </button>
      <?php } ?>
      <?php if ($type === 'sequence') { ?>

        <button type="button" class="btn btn-outline-warning px-5" id="delete_sequence" data-id="<?= esc($id) ?>"
          data-sequence-type="<?= esc($sequence_type ?? '') ?>">
          <i class="fas fa-trash"></i>
        </button>

      <?php } ?>
    </div>
  </div>
</div>