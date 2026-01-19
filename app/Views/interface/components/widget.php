<div class="row justify-content-center">
  <?php foreach ($widget as $key => $value) {  ?>
    <div class="col-xl-<?= $value['col-xl']; ?> col-lg-6 col-md-6 col-sm-12 col-12">
      <div class="card">
        <div class="card-body">
          <div class="d-inline-block">
            <h5 class="text-muted"><?= $value['text']; ?></h5>
            <h2 class="mb-0"> <?= $value['number']; ?></h2>
          </div>
          <div class="float-right icon-circle-medium  icon-box-lg  bg-info-light mt-1">
            <i class="<?= $value['icon']; ?>"></i>
          </div>
        </div>
      </div>
    </div>
  <?php }  ?>
</div>