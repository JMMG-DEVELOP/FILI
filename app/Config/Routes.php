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
        $routes->post('products/product_open', 'Products\Products\Add::open');
        $routes->post('products/product_save', 'Products\Products\Add::save');
        $routes->post('products/product_save_verify', 'Products\Products\Add::code_verify');
        // Product Edit
        $routes->post('products/product_edit_open', 'Products\Products\Edit::open');
        $routes->post('products/product_edit_save', 'Products\Products\Edit::save');



        // Brands Datatable
        $routes->post(
            'brands/datatable',
            'Products\Brands\Datatable::datatable'
        );
        // Brands Add
        $routes->post('brands/brand_open', 'Products\Brands\Add::open');

        // Section Datatable
        $routes->post(
            'section/datatable',
            'Products\Section\Datatable::datatable'
        );
        // Section Add
        $routes->post('section/section_open', 'Products\Section\Add::open');


    });

});

