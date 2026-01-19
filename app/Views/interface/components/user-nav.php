<a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?= base_url(); ?>/assets/images/avatar-1.jpg" alt="" class="user-avatar-md rounded-circle"></a>
<div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
  <div class="nav-user-info">
    <h5 class="mb-0 text-white nav-user-name">
      <?= $user_name ?>
    </h5>
    <span class="status"></span><span class="ml-2"><?= $category_name ?></span>
  </div>
  <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Configuración</a>
  <a class="dropdown-item" href="#"><i class="fas fa-power-off mr-2"></i>Cerrar Sesión</a>
</div>