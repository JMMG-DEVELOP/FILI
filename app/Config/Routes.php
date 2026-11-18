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
$routes->get('health', 'Health::index');

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
        $routes->get('/', 'Products\Access::index');

        $routes->group('panels', function ($routes) {
            $routes->post('products', 'Products\Access::products_panel');
        });

        // ------------------------------------------
        // PRODUCTS
        // ------------------------------------------
        $routes->group('products', function ($routes) {

            $routes->group('home', function ($routes) {

                $routes->group('home', function ($routes) {
                    $routes->post('panel_load', 'Products\Products\Home\Home::panel_load');
                    $routes->post('datatable', 'Products\Products\Home\Home::datatable');
                    $routes->post('updateInlineField', 'Products\Products\Home\Home::updateInlineField');


                });
                $routes->group('view', function ($routes) {

                    $routes->post('panel_load', 'Products\Products\Home\View::panel_load');

                });
                $routes->group('print', function ($routes) {

                    $routes->post('panel_load', 'Products\Products\Home\Prints::panel_load');

                });
            });

            $routes->group('form', function ($routes) {
                $routes->group('product', function ($routes) {
                    $routes->post('open', 'Products\Products\Form\Product::open');
                    $routes->post('code_validation', 'Products\Products\Form\Product::code_validation', ['filter' => 'ajax']);
                    $routes->post('confirm_open', 'Products\Products\Form\Product::confirm_open', ['filter' => 'ajax']);
                    $routes->post('create', 'Products\Products\Form\ProductCrud::create', ['filter' => 'ajax']);
                    $routes->post('update', 'Products\Products\Form\ProductCrud::update', ['filter' => 'ajax']);


                });

            });
            $routes->group('shopp', function ($routes) {

                $routes->post('shopp', 'Products\Products\Shopp\Shopp::panel_load');
                $routes->post('shopp_search', 'Products\Products\Shopp\Shopp::shopp_search');
                $routes->post('shopp_win', 'Products\Products\Shopp\Shopp::shopp_win');
                $routes->post('update_stock', 'Products\Products\Shopp\Shopp::update_stock');
                $routes->post('save', 'Products\Products\Shopp\Shopp::save');

            });

        });
        // ------------------------------------------
        // PROVIDER
        // ------------------------------------------

        $routes->group('provider', function ($routes) {

            $routes->post(
                'panel_load',
                'Products\Provider\Home::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Products\Provider\Home::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Products\Provider\Home::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Products\Provider\Home::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Products\Provider\Home::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Products\Provider\Home::delete_save'
            );

        });

        // ------------------------------------------
        // BRANDS
        // ------------------------------------------
        $routes->group('brand', function ($routes) {
            $routes->post(
                'panel_load',
                'Products\Brand\Brand::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Products\Brand\Brand::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Products\Brand\Brand::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Products\Brand\Brand::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Products\Brand\Brand::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Products\Brand\Brand::delete_save'
            );
        });


        // ------------------------------------------
        // SECTION
        // ------------------------------------------
        $routes->group('section', function ($routes) {
            $routes->post(
                'panel_load',
                'Products\Section\Section::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Products\Section\Section::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Products\Section\Section::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Products\Section\Section::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Products\Section\Section::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Products\Section\Section::delete_save'
            );
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
    // ------------------------------------------
    // IMPRESION
    // ------------------------------------------

    $routes->group('prints', function ($routes) {

        $routes->post(
            'ticket',
            'Prints\Ticket::print'
        );
        $routes->post(
            'ticket_pdf',
            'Prints\Ticket::pdf'
        );
        $routes->post(
            'invoice',
            'Prints\Invoice::print'
        );
        $routes->post(
            'invoice_pdf',
            'Prints\Invoice::pdf'
        );
        $routes->post(
            'generate_invoice_print',
            'Prints\GenerateInvoice::print'
        );
        $routes->post(
            'generate_invoice_pdf',
            'Prints\GenerateInvoice::pdf'
        );
        $routes->post(
            'barcode',
            'Prints\Code::barcode'
        );
        $routes->post(
            'name',
            'Prints\Name::print'
        );
        $routes->post(
            'label',
            'Prints\Label::print'
        );

    });

    // ==================================================
    // CUSTOMER
    // ==================================================
    $routes->group('customer', function ($routes) {

        $routes->get('/', 'Customer\Access::index');
        $routes->post(
            'delete_open',
            'Customer\Access::delete_open'
        );
        $routes->group('process', function ($routes) {

            $routes->post(
                'customer_panel_load',
                'Customer\Process::customer_panel_load'
            );

        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'panel_load',
                'Customer\Home\Home::panel_load'
            );
        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'datatable',
                'Customer\Home\Home::datatable'
            );
        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'form_new_open',
                'Customer\Home\Home::form_new_open'
            );
        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'form_new_save',
                'Customer\Home\Home::form_new_save'
            );
        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'form_edit_open',
                'Customer\Home\Home::form_edit_open'
            );
        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'form_edit_save',
                'Customer\Home\Home::form_edit_save'
            );
        });
        $routes->group('home', function ($routes) {
            $routes->post(
                'delete_save',
                'Customer\Home\Home::delete_save'
            );
        });
        // VIEWS
        $routes->group('view', function ($routes) {
            $routes->post(
                'view_panel_load',
                'Customer\View\View::view_panel_load'
            );
            $routes->post(
                'home_view_load',
                'Customer\View\View::home_view_load'
            );
            $routes->post(
                'credit_payment_form_open',
                'Customer\View\View::credit_payment_form_open'
            );
            $routes->post(
                'credit_payment_form_save',
                'Customer\View\View::credit_payment_form_save'
            );
            $routes->post(
                'credit_add_form_open',
                'Customer\View\View::credit_add_form_open'
            );
            $routes->post(
                'credit_add_form_save',
                'Customer\View\View::credit_add_form_save'
            );

        });
        // CUSTOMER SALES
        $routes->group('sales', function ($routes) {
            $routes->post(
                'panel_load',
                'Customer\Sales\Sales::panel_load'
            );
            $routes->post(
                'sales_datatable',
                'Customer\Sales\Sales::sales_datatable'
            );
            $routes->post(
                'sales_card',
                'Customer\Sales\Sales::sales_card'
            );


        });
        // CUSTOMER SALES
        $routes->group('details', function ($routes) {
            $routes->post(
                'panel_load',
                'Customer\Sales\Details::panel_load'
            );
            $routes->post(
                'anoter_panel_load',
                'Customer\Sales\Details::anoter_panel_load'
            );
            $routes->post(
                'anoter_datatable',
                'Customer\Sales\Details::anoter_datatable'
            );
        });
        $routes->group('payment', function ($routes) {
            $routes->post(
                'panel_load',
                'Customer\Payment\Home::panel_load'
            );
            $routes->post(
                'payment_datatable',
                'Customer\Payment\Home::payment_datatable'
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
        $routes->group('point_asignation', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Points\Asignation::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Points\Asignation::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Points\Asignation::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Points\Asignation::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Points\Asignation::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Points\Asignation::delete_save'
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
        $routes->group('sucursal_asignation', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Users\Asignation::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Users\Asignation::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Users\Asignation::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Users\Asignation::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Users\Asignation::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Users\Asignation::delete_save'
            );

        });
        $routes->group('sucursal', function ($routes) {

            $routes->post(
                'panel_load',
                'Config\Sucursals\Sucursal::panel_load'
            );
            $routes->post(
                'form_new_open',
                'Config\Sucursals\Sucursal::form_new_open'
            );
            $routes->post(
                'form_new_save',
                'Config\Sucursals\Sucursal::form_new_save'
            );
            $routes->post(
                'form_edit_open',
                'Config\Sucursals\Sucursal::form_edit_open'
            );
            $routes->post(
                'form_edit_save',
                'Config\Sucursals\Sucursal::form_edit_save'
            );
            $routes->post(
                'delete_save',
                'Config\Sucursals\Sucursal::delete_save'
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
    // INVOICE - CONTROL DE FACTURA LEGAL
    // 
    // ==================================================
    $routes->group('invoice', ['filter' => 'permission:access_invoice'], function ($routes) {
        $routes->get('/', 'Invoices\Access::index');

        $routes->group('home', function ($routes) {

            $routes->post(
                'panel_load',
                'Invoices\Home::panel_load'
            );

            $routes->post(
                'home_datatable',
                'Invoices\Home::home_datatable'
            );
            $routes->post(
                'cancel_invoice',
                'Invoices\Home::cancel_invoice'
            );
            $routes->post(
                'card_load',
                'Invoices\Home::card_load'
            );
            $routes->post(
                'reprint',
                'Invoices\Home::reprint'
            );
        });
        $routes->group('create', function ($routes) {

            $routes->post(
                'form_open',
                'Invoices\Create::form_open'
            );
            $routes->post(
                'sales_datatable',
                'Invoices\Create::sales_datatable'
            );
            $routes->post(
                'sales_panel',
                'Invoices\Create::sales_panel'
            );
            $routes->post(
                'sales_detail_panel',
                'Invoices\Create::sales_detail_panel'
            );
            $routes->post(
                'save_invoice',
                'Invoices\Create::save_invoice'
            );
            $routes->post(
                'sales_add_invoice',
                'Invoices\Create::sales_add_invoice'
            );
            $routes->post(
                'invoice_detail_panel',
                'Invoices\Create::invoice_detail_panel'
            );
        });

        $routes->group('', function ($routes) {

            $routes->post(
                'panel_load',
                'Invoices\Home::panel_load'
            );
        });
    });

});