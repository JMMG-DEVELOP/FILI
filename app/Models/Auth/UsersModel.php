<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $customErrors = [];

    protected $allowedFields = [
        'name',
        'password',
        'status',
        'ci',
        'category',
        'user',
        'phone'
    ];
    public function edit($data, $id)
    {
        return $this
            ->where('id', $id)
            ->set($data)
            ->update();
    }
    public function get_ById($id)
    {
        return $this
            ->select([
                'users.id',
                'users.name',
                'users.ci',
                'users.user',
                'users.phone',

                'users.status',
                'status.name AS status_name',

                'users.category',
                'users_category.name AS category_name'
            ])
            ->join(
                'status',
                'status.id = users.status',
                'left'
            )
            ->join(
                'users_category',
                'users_category.id = users.category',
                'left'
            )
            ->where('users.id', $id)
            ->first();
    }
    public function get_All()
    {
        return $this->select([
            'users.id',
            'users.name',
            'users.ci',
            'users.user',
            'users.phone',
            'status.name AS status_name',
            'users_category.name AS category_name'
        ])
            ->join(
                'status',
                'status.id = users.status',
                'left'
            )
            ->join(
                'users_category',
                'users_category.id = users.category',
                'left'
            )
            ->findAll();
    }
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
    public function add($values)
    {
        $user = trim($values['user'] ?? '');

        if ($user === '') {
            $this->customErrors = [
                'user' => 'EL USUARIO ES OBLIGATORIO'
            ];

            return false;
        }

        $exists = $this
            ->where('user', $user)
            ->first();

        if ($exists) {
            $this->customErrors = [
                'user' => 'EL USUARIO YA EXISTE'
            ];

            return false;
        }

        return $this->insert($values);
    }

    public function getCustomErrors()
    {
        return $this->customErrors;
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