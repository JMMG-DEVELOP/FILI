<?php

namespace App\Models\Products\Section;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table = 'products_section';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name'
    ];


}