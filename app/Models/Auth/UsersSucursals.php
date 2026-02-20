<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersSucursals extends Model
{
    protected $table = 'users_sucursals';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user',
        'sucursal',
    ];

    public function details($user)
    {
        return $this->select([
            'users_sucursals.sucursal',
            'sucursals.name AS sucursal_name'
        ])
            ->join('sucursals', 'sucursals.id = users_sucursals.sucursal')
            ->where('users_sucursals.user', $user)
            ->findAll();
    }




}