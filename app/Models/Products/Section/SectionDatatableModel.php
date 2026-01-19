<?php

namespace App\Models\Products\Section;

use App\Models\BaseDatatableModel;

class SectionDatatableModel extends BaseDatatableModel
{
    protected $table = 'products_section';

    protected array $select = [
        'products_section.id',
        'products_section.name',
    ];

    protected array $columnOrder = [
        null,                       // number
        'products_section.name',    // name
        null,                       // edit
        null                        // delete
    ];

    protected array $columnSearch = [
        'products_section.name',
    ];

    protected array $defaultOrder = [
        'products_section .id' => 'DESC'
    ];
}
