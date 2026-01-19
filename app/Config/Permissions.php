<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Permissions extends BaseConfig
{

    public array $details = [

        // ROOT
        1 => [
            // '*',
            //  Productos
            'products_access', //Acceder por menu y route

            'product_products_view',
            'product_product_add',
            'product_product_edit',
            'product_product_delete',

            // Costos de Productos
            'product_cost_view',
            'product_cost_edit',

            // Margen de Productos
            'product_margin_view',

            // Marcas de Productos
            'product_brands_view',
            'product_brand_add',
            'product_brand_edit',
            'product_brand_delete',

            // Seccion de Productos
            'product_sections_view',
            'product_section_add',
            'product_section_edit',
            'product_section_delete',
        ],

        // ADMIN
        2 => [
            //  Productos
            'products_access',

            'product_products_view',
            'product_product_add',
            'product_product_edit',
            // 'product_product_delete',

            // Costos de Productos
            'product_cost_view',

            // Margen de Productos
            'product_margin_view',

            // Marcas de Productos
            'product_brands_view',
            'product_brand_add',

            // Seccion de Productos
            'product_sections_view',
            'product_section_add',

            // '*',
        ],

        // VISOR
        3 => [
            'view_sucursales',
        ],

        // BOX
        4 => [],
    ];
}
