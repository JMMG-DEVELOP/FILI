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
    $routes->group('products', ['filter' => 'permission:products_access'], function ($routes) {

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
    $routes->group('box', ['filter' => 'permission:box_access'], function ($routes) {

        // ------------------------------------------
        // ACCESS
        // ------------------------------------------
        $routes->get('/', 'Box\Access::index');


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
                'sales_procedures_other_payment',
                'Box\Sales::sales_procedures_other_payment',
                ['filter' => 'ajax']
            );


        });

    });


    // ==================================================
    // CUSTOMER
    // Permiso: customer_access
    // ==================================================
    $routes->group('customer', ['filter' => 'permission:customer_access'], function ($routes) {

        $routes->group('process', function ($routes) {

            $routes->post(
                'customer_panel_load',
                'Customer\Process::customer_panel_load'
            );

        });

    });

});