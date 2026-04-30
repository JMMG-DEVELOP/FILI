<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ------------------------------
// RUTAS PÚBLICAS
// ------------------------------
$routes->get('/', 'Auth\Login::index');
$routes->post('login', 'Auth\Login::auth');
$routes->post('auth', 'Auth\Login::auth');

$routes->get('logout', 'Auth\Logout::index');

// ------------------------------
// GRUPO PROTEGIDO (AUTH)
// ------------------------------
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard (solo login)
    $routes->get('dashboard', 'Auth\Dashboard::index');

    // ==========================
    // PRODUCTOS
    // Requiere permiso: access_products
    // ==========================
    $routes->group('products', ['filter' => 'permission:products_access'], function ($routes) {

        $routes->get('/', 'Products\Products\Access::index');
        $routes->post
        ('panel', 'Products\Products\Access::panel');

        // Products Datatable
        $routes->post(
            'products/datatable',
            'Products\Products\Datatable::datatable'
        );
        // Product Add
        $routes->post('products/product_open', 'Products\Products\Add::open', ['filter' => 'ajax']);
        $routes->post('products/product_save', 'Products\Products\Add::save', ['filter' => 'ajax']);
        $routes->post('products/product_save_verify', 'Products\Products\Add::code_verify');
        // Product Edit
        $routes->post('products/product_edit_open', 'Products\Products\Edit::open', ['filter' => 'ajax']);
        $routes->post('products/product_edit_save', 'Products\Products\Edit::save', ['filter' => 'ajax']);



        // Brands Datatable
        $routes->post(
            'brands/datatable',
            'Products\Brands\Datatable::datatable'
        );
        // Brands Add
        $routes->post('brands/brand_open', 'Products\Brands\Add::open', ['filter' => 'ajax']);

        // Section Datatable
        $routes->post(
            'section/datatable',
            'Products\Section\Datatable::datatable'
        );
        // Section Add
        $routes->post('section/section_open', 'Products\Section\Add::open', ['filter' => 'ajax']);


    });

    // ==========================
    // BOX
    // Requiere permiso:box_access
    // ==========================
    $routes->group('box', ['filter' => 'permission:box_access'], function ($routes) {

        $routes->get('/', 'Box\Access::index');

        $routes->post('process/controller_panel_load', 'Box\Process::controller_panel_load', ['filter' => 'ajax']);

        $routes->post('process/payment_panel_load', 'Box\Process::payment_panel_load', ['filter' => 'ajax']);

        $routes->post('process/expedition_point_load', 'Box\Process::expedition_point_load', ['filter' => 'ajax']);

        $routes->post('process/sales_cash_credit_confirm', 'Box\Process::sales_cash_credit_confirm', ['filter' => 'ajax']);

        $routes->post('process/expedition_point_select', 'Box\Process::expedition_point_select', ['filter' => 'ajax']);

        $routes->post('controller/product_search', 'Box\Controller::product_search', ['filter' => 'ajax']);
        $routes->post('controller/product_form', 'Box\Controller::product_form', ['filter' => 'ajax']);

        //Customer

        $routes->post('controller/customer_search', 'Customer\Process::search', ['filter' => 'ajax']);

        $routes->post('controller/customer_form', 'Customer\Add::open', ['filter' => 'ajax']);
        $routes->post('controller/customer_add', 'Customer\Add::save');


        // Invoice
        $routes->post('invoice/product_add', 'Box\Invoice::product_add', ['filter' => 'ajax']);


        // SALES

        $routes->post('sales/sales_cash_payment', 'Box\Sales::sales_cash_payment', ['filter' => 'ajax']);

        $routes->post('sales/sales_procedures_credit_payment', 'Box\Sales::sales_procedures_credit_payment', ['filter' => 'ajax']);

        $routes->post('sales/sales_cash_credit_payment', 'Box\Sales::sales_cash_credit_payment', ['filter' => 'ajax']);

    });

    // ==========================
    // CUSTOMER
    // Requiere permiso:customer_access
    // ==========================
    $routes->group('customer', ['filter' => 'permission:customer_access'], function ($routes) {
        $routes->post('process/customer_panel_load', 'Customer\Process::customer_panel_load');


    });

});

