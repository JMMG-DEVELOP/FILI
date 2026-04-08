<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class UsersSessionsModel extends Model
{
    protected $table = 'users_sessions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'date',
        'start',
        'close',
        'user',
        'device',
        'ip',
        'ip_true',
        'browser',
        'type',
        'user_agent',
        'device_hint',
        'status'
    ];

    public function start($session)
    {
        $existing = $this->verify($session['user']);

        if ($existing) {
            return $existing['id']; // 
        }

        $this->insert($session);

        return $this->insertID();
    }

    public function verify($user)
    {
        return $this->where('user', $user)
            ->where('status', '1')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function isNewIP($user, $ip)
    {
        return !$this->where('user', $user)
            ->where('ip', $ip)
            ->first();
    }

    function getCountry($ip)
    {
        $json = file_get_contents("http://ip-api.com/json/$ip");
        $data = json_decode($json, true);

        return $data['country'] ?? 'Unknown';
    }
}