<!-- <li class="nav-item dropdown connection"> -->

<a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
  aria-expanded="false"> <i class="fas fa-fw fa-th"></i> </a>
<ul class="dropdown-menu dropdown-menu-right connection-dropdown">

  <li class="connection-list">
    <div class="row">
      <?php if (can('access_product')): ?>
        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
          <a href="<?= base_url('products'); ?>" class="connection-item"><img
              src="<?= base_url(); ?>/assets/images/github.png" alt=""> <span> Productos</span></a>
        </div>
      <?php endif; ?>
      <?php if (can('access_box')): ?>
        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
          <a href="<?= base_url('box'); ?>" class="connection-item"><img src="<?= base_url(); ?>/assets/images/github.png"
              alt=""> <span>
              Caja</span></a>
        </div>
      <?php endif; ?>


    </div>
    <div class="row">


      <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
        <?php if (can('access_config')): ?>
          <a href="<?= base_url('config'); ?>" class="connection-item"><img
              src="<?= base_url(); ?>/assets/images/github.png" alt=""> <span>
              Config</span></a>

        <?php endif; ?>

      </div>

    </div>
  </li>
</ul>

<!-- </li> -->