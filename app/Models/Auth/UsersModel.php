<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'password',
        'status',
        'ci',
        'category',
        'user'
    ];

    public function details($user)
    {
        return $this->select([
            'users.id',
            'users.name',
            'users.user',
            'users.category',
            'users_category.name AS category_name'
        ])
        ->join('users_category', 'users_category.id = users.category')
        ->where('users.id', $user)
        ->get()
        ->getRowArray();
    }

    public function login($username, $password)
    {
         $user = $this->where(['user' => $username, 'status' => 1])->first();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}