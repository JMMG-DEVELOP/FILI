<!-- <li class="nav-item dropdown connection"> -->

<a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
  aria-expanded="false"> <i class="fas fa-fw fa-th"></i> </a>
<ul class="dropdown-menu dropdown-menu-right connection-dropdown">

  <li class="connection-list">
    <div class="row">

      <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
        <?php if (can('products_access')): ?>
          <a href="<?= base_url('products'); ?>" class="connection-item"><img
              src="<?= base_url(); ?>/assets/images/github.png" alt=""> <span> Productos</span></a>

        <?php endif; ?>

      </div>
      <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
        <?php if (can('access_customers')): ?>
          <a href="#" class="connection-item"><img src="<?= base_url(); ?>/assets/images/github.png" alt=""> <span>
              Clientes</span></a>

        <?php endif; ?>

      </div>


    </div>
    <div class="row">

      <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
        <?php if (can('access_sucursals')): ?>
          <a href="#" class="connection-item"><img src="<?= base_url(); ?>/assets/images/github.png" alt=""> <span>
              Sucursales</span></a>

        <?php endif; ?>

      </div>
      <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 ">
        <?php if (can('access_users')): ?>
          <a href="#" class="connection-item"><img src="<?= base_url(); ?>/assets/images/github.png" alt=""> <span>
              Usuarios</span></a>

        <?php endif; ?>

      </div>

    </div>
  </li>
</ul>

<!-- </li> -->