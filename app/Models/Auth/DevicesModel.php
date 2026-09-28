<?php

namespace App\Models\Auth;

use CodeIgniter\Model;

class DevicesModel extends Model
{
  protected $table = 'devices';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'uuid',
    'name',
    'type',
    'enabled',
  ];
  public function getDeviceSession($sessionId)
  {
    $builder = $this->db->table('users_sessions us');

    $builder->select('
            us.id AS session_id,
            us.date AS session_date,
            us.start AS session_start,
            us.close AS session_close,
            us.user AS session_user,
            us.device AS session_device,
            us.ip AS session_ip,
            us.ip_true AS session_ip_true,
            us.browser AS session_browser,
            us.type AS session_type,
            us.user_agent AS session_user_agent,
            us.device_hint AS session_device_hint,
            us.status AS session_status,

            d.id AS device_id,
            d.uuid AS device_uuid,
            d.name AS device_name,
            d.type AS device_type,
            d.enabled AS device_enabled
        ');

    $builder->join(
      'devices d',
      'd.id = us.device',
      'left'
    );

    $builder->where('us.id', $sessionId);

    return $builder->get()->getRowArray();
  }

}