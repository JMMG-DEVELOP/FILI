<div class="dashboard-header">
  <nav class="navbar navbar-expand-lg bg-white fixed-top">

    <a class="navbar-brand" href="#">F I L I</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">

      <!-- 🔹 Nombre de usuario (lado izquierdo) -->
      <div class="navbar-text user-name ml-3">
        <?= esc($user_name) ?>
      </div>

      <!-- 🔹 Menús a la derecha -->
      <ul class="navbar-nav ml-auto navbar-right-top">
        <li>
          <div id="fili_connection_status" class="badge badge-secondary" data-server-url="<?= base_url('health') ?>">
            <i class="fas fa-sync-alt"></i>
            Verificando conexión...
          </div>
        </li>

        <li class="nav-item dropdown connection">
          <?= $this->include('interface/components/nav'); ?>
        </li>

        <li class="nav-item dropdown nav-user">
          <?= $this->include('interface/components/user-nav'); ?>
        </li>

      </ul>

    </div>
  </nav>
</div>