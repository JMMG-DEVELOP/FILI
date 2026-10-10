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
            'access_product', //Acceder por menu y route

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

            // Stock de Productos
            'product_stock_view',
            'product_stock_edit',

            // Imprimir Codigos de Barra
            'product_code_print',

            //
            //  BOX
            //
            'access_box', //Acceder por menu y route

            //
            //  CUSTOMER
            //
            'access_customer', //Acceder por menu y route

            //
            //  USERS
            //
            'access_users', //Acceder por menu y route
            //*********************** 
            //  ¨¨¨¨ CONFIG
            // Si tiene edit, no habilitar view, si no tiene view habilitado solo podra ver el datatable, si tiene, podra abrir el formulario sin poder editar
            'access_config', //Acceder por menu y route
            // ¨¨¨¨ PRINTERS
            // 'printer_view',
            'printer_edit',
            // 'printer_delete',
            'printer_add',
            // ¨¨¨¨ DRIVERS
            // 'driver_view',
            'driver_edit',
            // 'driver_delete',
            'driver_add',
            // ¨¨¨¨ PAPERS
            // 'paper_view',
            'paper_edit',
            // 'paper_delete',
            'paper_add',
            // ¨¨¨¨ DEVICES
            // 'device_view',
            'device_edit',
            // 'device_delete',
            'device_add',
            // ¨¨¨¨ USERS
            // 'user_view',
            'user_edit',
            // 'user_delete',
            'user_add',
            // ¨¨¨¨ CATEGORIAS DE USUAIO
            // 'category_view',
            'category_edit',
            // 'category_delete',
            'category_add',
            // ¨¨¨¨ ASIGNACION DE IMPRESORAS A DISPOSITIVOS
            // 'printerAsignation_view',
            'printerAsignation_edit',
            'printerAsignation_delete',
            'printerAsignation_add',
            // ¨¨¨¨ PUNTO DE EXPEDICION
            // 'point_point_view',
            'point_point_edit',
            'point_point_delete',
            'point_point_add',
            // ¨¨¨¨ SECUENCIAS DE PUNTO DE EXPEDICIÓN
            // 'sequence_view',
            'sequence_edit',
            'sequence_delete',
            'sequence_add',
            // ¨¨¨¨ ASIGNACION DE PUNTO DE EXPEDICION A DISPOSITIVOS
            // 'point_asignation_view',
            'point_asignation_edit',
            'point_asignation_delete',
            'point_asignation_add',
            // ¨¨¨¨ ASIGNACION DE SUCURSAL A USUARIOS
            // 'sucursal_asignation_view',
            'sucursal_asignation_edit',
            'sucursal_asignation_delete',
            'sucursal_asignation_add',
            // ¨¨¨¨ MANEJO DE SUCURSALES
            // 'sucursal_view',
            'sucursal_edit',
            'sucursal_delete',
            'sucursal_add',
        ],

        // ADMIN
        2 => [

        ],

        // USER
        3 => [

        ],

        // BOX
        4 => [
            'access_box', //Acceder por menu y route
        ],
    ];
}
