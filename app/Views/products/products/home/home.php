<?php
$canViewCost = in_array(
  'product_cost_view',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);

$canEditProduct = in_array(
  'product_product_edit',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);

$canViewProduct = in_array(
  'product_product_view',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);

$canDeleteProduct = in_array(
  'product_product_delete',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);

$canAddProduct = in_array(
  'product_product_add',
  session()->get('permissions') ?? []
) || in_array('*', session()->get('permissions') ?? []);
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">ADMINISTRACIÓN DE PRODUCTOS</h5>


      <?php if (can('product_product_add')): ?>
        <a href="javascript:void(0)" class="btn btn-outline-primary product_form_add_open"> <i class="fas fa-plus"></i>
        </a>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">

        <table id="productsTable" class="table table-striped table-bordered dt-responsive nowrap">
          <thead class="table table-striped">
            <tr>
              <th>CÓDIGO</th>
              <th>DESCRIPCIÓN</th>
              <th>SECCIÓN</th>

              <?php if ($canViewCost): ?>
                <th>COSTO</th>
              <?php endif; ?>

              <th>PRECIO 1</th>
              <th>PRECIO 2</th>
              <th>PRECIO 3</th>

              <th>FILI CENTRO</th>
              <th>FILI 2</th>

              <th>PRINT</th>

              <?php if ($canViewProduct): ?>
                <th>VER</th>
              <?php endif; ?>


              <?php if (can('product_product_edit')): ?>
                <th>Editar</th>
              <?php endif; ?>

              <?php if (can('product_product_delete')): ?>
                <th>Eliminar</th>
              <?php endif; ?>
            </tr>
          </thead>
        </table>
      </div>
    </div>

  </div>
</div>