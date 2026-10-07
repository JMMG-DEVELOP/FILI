<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ======================================================
// PUBLICAS
// ======================================================
$routes->get('/', 'Auth\Login::index');
$routes->post('login', 'Auth\Login::auth');
$routes->post('auth', 'Auth\Login::auth');
$routes->get('logout', 'Auth\Logout::index');


// ======================================================
// RUTAS PROTEGIDAS (AUTH)
// ======================================================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // ==================================================
    // DASHBOARD
    // ==================================================
    $routes->get('dashboard', 'Auth\Dashboard::index');


    // ==================================================
    // PRODUCTS
    // Permiso: products_access
    // ==================================================
    $routes->group('products', ['filter' => 'permission:access_product'], function ($routes) {

        // ------------------------------------------
        // ACCESS
        // ------------------------------------------
        $routes->get('/', 'Products\Products\Access::index');
        $routes->post('panel', 'Products\Products\Access::panel');


        // ------------------------------------------
        // PRODUCTS
        // ------------------------------------------
        $routes->group('products', function ($routes) {

            // Datatable
            $routes->post('datatable', 'Products\Products\Datatable::datatable');

            // Add
            $routes->post('product_open', 'Products\Products\Add::open', ['filter' => 'ajax']);
            $routes->post('product_save', 'Products\Products\Add::save', ['filter' => 'ajax']);
            $routes->post('product_save_verify', 'Products\Products\Add::code_verify');

            // Edit
            $routes->post('product_edit_open', 'Products\Products\Edit::open', ['filter' => 'ajax']);
            $routes->post('product_edit_save', 'Products\Products\Edit::save', ['filter' => 'ajax']);
        });


        // ------------------------------------------
        // BRANDS
        // ------------------------------------------
        $routes->group('brands', function ($routes) {

            // Datatable
            $routes->post('datatable', 'Products\Brands\Datatable::datatable');

            // Add
            $routes->post('brand_open', 'Products\Brands\Add::open', ['filter' => 'ajax']);
        });


        // ------------------------------------------
        // SECTION
        // ------------------------------------------
        $routes->group('section', function ($routes) {

            // Datatable
            $routes->post('datatable', 'Products\Section\Datatable::datatable');

            // Add
            $routes->post('section_open', 'Products\Section\Add::open', ['filter' => 'ajax']);
        });

    });


    // ==================================================
    // BOX
    // Permiso: box_access
    // ==================================================
    $routes->group('box', ['filter' => 'permission:access_box'], function ($routes) {

        // ------------------------------------------
        // ACCESS
        // ------------------------------------------
        $routes->get('/', 'Box\Access::index');
        $routes->get('close', 'Box\Close::index');
        $routes->get('open', 'Box\Access::open');


        // ------------------------------------------
        // CIERE DE CAJA
        // ------------------------------------------

        $routes->group('close', function ($routes) {

            $routes->post(
                'box_close_save',
                'Box\Close::box_close_save'
            );
            $routes->post(
                'box_close_resume',
                'Box\Close::box_close_resume'
            );


        });


        // ------------------------------------------
        // PROCESS
        // ------------------------------------------
        $routes->group('process', function ($routes) {

            $routes->post('controller_panel_load', 'Box\Process::controller_panel_load', ['filter' => 'ajax']);
            $routes->post('print_panel_load', 'Box\Process::print_panel_load', ['filter' => 'ajax']);
            $routes->post('payment_panel_load', 'Box\Process::payment_panel_load', ['filter' => 'ajax']);
            $routes->post('expedition_point_load', 'Box\Process::expedition_point_load', ['filter' => 'ajax']);
            $routes->post('box_movement_panel_load', 'Box\Process::box_movement_panel_load', ['filter' => 'ajax']);
            $routes->post('history_sales_panel_load', 'Box\Process::history_sales_panel_load', ['filter' => 'ajax']);
            $routes->post('history_sales_details_panel_load', 'Box\Process::history_sales_details_panel_load', ['filter' => 'ajax']);
            $routes->post('history_movements_panel_load', 'Box\Process::history_movements_panel_load', ['filter' => 'ajax']);
            $routes->post('invoice_cash_panel', 'Box\Process::invoice_cash_panel_load', ['filter' => 'ajax']);
            $routes->post('invoice_multi_payment_load', 'Box\Process::invoice_multi_payment_load', ['filter' => 'ajax']);
            $routes->post('expedition_point_select', 'Box\Process::expedition_point_select', ['filter' => 'ajax']);
            $routes->post('wait_panel_load', 'Box\Process::wait_panel_load', ['filter' => 'ajax']);
            $routes->post('invoice_product_panel', 'Box\Process::invoice_product_panel_load', ['filter' => 'ajax']);
            $routes->post('invoice_digits_panel', 'Box\Process::invoice_digits_panel_load', ['filter' => 'ajax']);


        });
        // ------------------------------------------
        // WAIT
        // ------------------------------------------
        $routes->group('wait', function ($routes) {
            $routes->post('wait_validation', 'Box\Wait::validation', ['filter' => 'ajax']);

            $routes->post('wait_save', 'Box\Wait::wait_save', ['filter' => 'ajax']);
            $routes->post('wait_update', 'Box\Wait::update', ['filter' => 'ajax']);
            $routes->post('wait_list', 'Box\Wait::list', ['filter' => 'ajax']);
            $routes->post('wait_delete', 'Box\Wait::wait_delete', ['filter' => 'ajax']);

        });

        // ------------------------------------------
        // ORDERS
        // ------------------------------------------
        $routes->group('orders', function ($routes) {
            $routes->post('order_validation', 'Box\orders::validation', ['filter' => 'ajax']);
            $routes->post('order_add', 'Box\orders::order_add', ['filter' => 'ajax']);

        });

        // ------------------------------------------
        // CONTROLLER
        // ------------------------------------------
        $routes->group('controller', function ($routes) {

            // Apertura de Caja
            $routes->post('open_box', 'Box\Controller::open_box', ['filter' => 'ajax']);

            // Products
            $routes->post('product_search', 'Box\Controller::product_search', ['filter' => 'ajax']);
            $routes->post('product_form', 'Box\Controller::product_form', ['filter' => 'ajax']);

            // Box movement
            $routes->post('box_movement_send', 'Box\Controller::box_movement_send', ['filter' => 'ajax']);

            // Customer
            $routes->post('customer_search', 'Customer\Process::search', ['filter' => 'ajax']);
            $routes->post('customer_form', 'Customer\Add::open', ['filter' => 'ajax']);
            $routes->post('customer_add', 'Customer\Add::save');
        });


        // ------------------------------------------
        // INVOICE
        // ------------------------------------------
        $routes->group('invoice', function ($routes) {

            $routes->post('product_add', 'Box\Invoice::product_add', ['filter' => 'ajax']);

        });


        // ------------------------------------------
        // SALES
        // ------------------------------------------
        $routes->group('sales', function ($routes) {
            $routes->post(
                'sales_devolution',
                'Box\Sales::sales_devolution',
                ['filter' => 'ajax']
            );

            $routes->post('sales_cash_payment', 'Box\Sales::sales_cash_payment', ['filter' => 'ajax']);

            $routes->post(
                'sales_digist_payment',
                'Box\Sales::sales_digist_payment',
                ['filter' => 'ajax']
            );
            $routes->post(
                'sales_credit_payment',
                'Box\Sales::sales_credit_payment',
                ['filter' => 'ajax']
            );

            $routes->post(
                'sales_cash_credit_payment',
                'Box\Sales::sales_cash_credit_payment',
                ['filter' => 'ajax']
            );

            $routes->post(
                'sales_cash_digist_payment',
                'Box\Sales::sales_cash_digist_payment',
                ['filter' => 'ajax']
            );

            $routes->post(
                'sales_cash_null',
                'Box\Sales::sales_cash_null',
                ['filter' => 'ajax']
            );

            $routes->post(
                'sales_credit_null',
                'Box\Sales::sales_credit_null',
                ['filter' => 'ajax']
            );


        });

    });


    // ==================================================
    // CUSTOMER
    // ==================================================
    $routes->group('customer', function ($routes) {

        $routes->group('process', function ($routes) {

            $routes->post(
                'customer_panel_load',
                'Customer\Process::customer_panel_load'
            );

        });

    });
    // ==================================================
    // CUSTOMERS
    // Permiso: customer_access
    // ==================================================

    // ==================================================
    // CONFIG
    // Permiso: access_config
    // ==================================================
    $routes->group('config', ['filter' => 'permission:access_config'], function ($routes) {
        $routes->get('/', 'Config\Access::index');

        $routes->post(
            'delete_open',
            'Config\Access::delete_open'
        );

        $routes->group('printers', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Printers\Printers::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Printers\Printers::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Printers\Printers::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Printers\Printers::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Printers\Printers::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Printers\Printers::delete_save'
            );

        });

        $routes->group('drivers', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Printers\Drivers::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Printers\Drivers::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Printers\Drivers::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Printers\Drivers::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Printers\Drivers::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Printers\Drivers::delete_save'
            );

        });

        $routes->group('papers', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Printers\Papers::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Printers\Papers::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Printers\Papers::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Printers\Papers::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Printers\Papers::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Printers\Papers::delete_save'
            );

        });
        $routes->group('point', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Points\Points::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Points\Points::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Points\Points::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Points\Points::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Points\Points::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Points\Points::delete_save'
            );

        });
        $routes->group('sequence', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Points\Sequence::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Points\Sequence::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Points\Sequence::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Points\Sequence::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Points\Sequence::form_edit_save'
            );
            $routes->post(
                'delete_open',
                'Config\Points\Sequence::delete_open'
            );
            $routes->post(
                'delete_save',
                'Config\Points\Sequence::delete_save'
            );

        });
        $routes->group('devices', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Printers\Devices::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Printers\Devices::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Printers\Devices::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Printers\Devices::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Printers\Devices::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Printers\Devices::delete_save'
            );

        });
        $routes->group('category', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Users\Category::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Users\Category::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Users\Category::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Users\Category::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Users\Category::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Users\Category::delete_save'
            );

        });

        $routes->group('asignation', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Printers\Asignation::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Printers\Asignation::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Printers\Asignation::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Printers\Asignation::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Printers\Asignation::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Printers\Asignation::delete_save'
            );

        });
        $routes->group('users', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Users\Users::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Users\Users::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Users\Users::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Users\Users::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Users\Users::form_edit_save'
            );
            $routes->post(
                'form_edit_password_save',
                'Config\Users\Users::form_edit_password_save'
            );
            $routes->post(
                'delete_save',
                'Config\Users\Users::delete_save'
            );

        });


    });

    // ==================================================
    // BOX CLOSE - CIERRE DE CAJA
    // 
    // ==================================================
    // $routes->group('box', function ($routes) {


    //     $routes->group('close', function ($routes) {

    //         $routes->post(
    //             'box_close_save',
    //             'Box\Close::box_close_save'
    //         );

    //     });

    // });

});