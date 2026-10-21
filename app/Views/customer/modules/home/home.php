<?php
$canView = can('customer_view');
$canAdd = can('customer_add');
$canEdit = can('customer_edit');
$canDelete = can('customer_delete');
?>

<div id="customer_permissions" data-can-view="<?= $canView ? '1' : '0' ?>" data-can-add="<?= $canAdd ? '1' : '0' ?>"
  data-can-edit="<?= $canEdit ? '1' : '0' ?>" data-can-delete="<?= $canDelete ? '1' : '0' ?>"></div>



<div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
  <div class="card">
    <div class="card-body">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Administración de Clientes</h3>

        <?php if (can('customer_add')): ?>
          <a href="#" class="btn btn-outline-primary" id="customer_form_new_open" title="Agregar cliente">
            <i class="fas fa-plus"></i>
          </a>
        <?php endif; ?>
      </div>

      <div class="table-responsive mt-3">

        <?php
        $canView = can('customer_view');
        $canEdit = can('customer_edit');
        $canDelete = can('customer_delete');
        ?>

        <table id="table_customer" class="table table-striped table-bordered dt-responsive nowrap" width="100%"
          data-can-view="<?= $canView ? '1' : '0' ?>" data-can-edit="<?= $canEdit ? '1' : '0' ?>"
          data-can-delete="<?= $canDelete ? '1' : '0' ?>">
          <thead>
            <tr>
              <th>ID</th>
              <th>CI / RUC</th>
              <th>Nombre</th>
              <th>Celular</th>
              <th>Correo</th>
              <th>Crédito</th>

              <?php if ($canView): ?>
                <th>Ver</th>
              <?php endif; ?>

              <?php if ($canEdit): ?>
                <th>Editar</th>
              <?php endif; ?>

              <?php if ($canDelete): ?>
                <th>Eliminar</th>
              <?php endif; ?>
            </tr>
          </thead>

          <tbody></tbody>
        </table>

      </div>

    </div>
  </div>
</div>