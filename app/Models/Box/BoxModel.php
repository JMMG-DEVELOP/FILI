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


  public function get_open_box($user)
  {
    return $this->where('user', $user)
      ->where('status', 1)
      ->orderBy('id', 'DESC')
      ->first();
  }


  public function close_box($box)
  {
    return $this->where('id', $box)
      ->where('status', 1)
      ->set([
        'status' => 2
      ])
      ->update();
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