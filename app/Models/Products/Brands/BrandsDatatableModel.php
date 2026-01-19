<?php

namespace App\Models\Products\Brands;

use App\Models\BaseDatatableModel;

class BrandsDatatableModel extends BaseDatatableModel
{
    protected $table = 'products_brands';

    protected array $select = [
        'products_brands.id',
        'products_brands.name',
    ];

    protected array $columnOrder = [
        null,                       // number
        'products_brands.name',     // name
        null,                       // edit
        null                        // delete
    ];

    protected array $columnSearch = [
        'products_brands.name',
    ];

    protected array $defaultOrder = [
        'products_brands.id' => 'DESC'
    ];
}
