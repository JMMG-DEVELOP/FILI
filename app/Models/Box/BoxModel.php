<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class BoxModel extends Model
{
  protected $table = 'box';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'user',
    'session',
    'status'
  ];

  public function add_box($data)
  {
    $this->insert($data);

    return $this->insertID();
  }

  public function close_box_by_session($sessionId)
  {
    return $this->where('session', $sessionId)
      ->where('status', 1)
      ->set([
        'status' => 2
      ])
      ->update();
  }

}