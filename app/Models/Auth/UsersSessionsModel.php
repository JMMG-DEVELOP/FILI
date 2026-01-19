<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersSessionsModel extends Model
{
    protected $table      = 'users_sessions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'date',
        'start',
        'close',
        'user',
        'device',
        'ip'
    ];

    public function start($session)
    {
        return $this->insert($session);
    }
}