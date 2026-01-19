<?php

function productPermissions(): array
{
  return [
    'view' => can('product_products_view'),
    'add' => can('product_product_add'),
    'edit' => can('product_product_edit'),
    'delete' => can('product_product_delete'),
    'cost' => can('product_cost_view'),
    'brands' => can('product_brands_view'),
    'section' => can('product_sections_view'),
  ];
}
