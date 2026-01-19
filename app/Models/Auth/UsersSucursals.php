<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersSucursals extends Model
{
    protected $table      = 'users_sucursals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user',
        'sucursal',
    ];

    public function details($user)
    {
        return $this->select([
            'sucursal',
        ])
        ->where('user', $user)
        ->findAll();

    }


}