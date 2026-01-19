<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersCategoryModel extends Model
{
    protected $table      = 'users_category';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name'
    ];

    public function login()
    {
        
    }
}