<?php
$canEditSection = in_array(
  'product_section_edit',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);
$canDeleteSection = in_array(
  'product_section_delete',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);
?>
<script>
  const CAN_EDIT_SECTION = <?= $canEditSection ? 'true' : 'false' ?>;
  const CAN_DELETE_SECTION = <?= $canDeleteSection ? 'true' : 'false' ?>;
</script>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
  <div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">ADMINISTRACIÓN DE SECCIONES</h5>

      <?php if (can('product_section_add')): ?>
        <a href="#" class="btn btn-outline-light section_add">Nuevo</a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        <table id="sectionTable" class="table table-bordered w-100">
          <thead class="table table-striped">
            <tr>
              <th>N°</th>
              <th>DESCRIPCIÓN</th>

              <?php if (can('product_section_edit')): ?>
                <th>EDITAR</th>
              <?php endif; ?>


              <?php if (can('product_section_delete')): ?>
                <th>ELIMINAR</th>
              <?php endif; ?>
            </tr>
          </thead>
        </table>

      </div>
    </div>
  </div>
</div>