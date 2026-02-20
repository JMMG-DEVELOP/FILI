<?php

namespace App\Models\Products\Products;

use App\Models\BaseDatatableModel;

class ProductDatatableModel extends BaseDatatableModel
{
    protected $table = 'products p';

    protected array $select = [
        'p.id',
        'p.code',
        'p.description',
        's.name AS section',
        'COALESCE(pc.cost, 0) AS cost',
        'COALESCE(pp.price_one, 0) AS price_1',
        'COALESCE(pp.price_two, 0) AS price_2',
        'COALESCE(pp.price_card, 0) AS price_card',
        'COALESCE(ps1.stock, 0) AS stock_s1',
        'COALESCE(ps2.stock, 0) AS stock_s2',
    ];

    protected array $joins = [
        ['products_section s', 's.id = p.section', 'left'],
        ['products_cost pc', 'pc.product = p.code', 'left'],
        ['products_prices pp', 'pp.product = p.code', 'left'],
        ['products_stock ps1', "ps1.product = p.code AND ps1.sucursal = 1", 'left'],
        ['products_stock ps2', "ps2.product = p.code AND ps2.sucursal = 2", 'left'],
    ];

    protected array $columnSearch = [
        'p.code',
        'p.description',
    ];

    protected array $columnSearchCount = [
        'code',
        'description',
    ];

    protected array $defaultOrder = ['p.id' => 'DESC'];
}


