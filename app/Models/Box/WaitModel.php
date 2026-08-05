<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class WaitModel extends Model
{
  protected $table = 'wait';

  protected $primaryKey = 'id';

  protected $returnType = 'array';

  protected $allowedFields = [
    'mount',
    'customer',
    'date',
    'time',
    'user'
  ];

  public function add_wait($data)
  {
    if (!$this->insert($data)) {

      return [

        'status' => false,
        'errors' => $this->errors(),
        'db' => $this->db->error(),
        'data' => $data
      ];
    }

    return [

      'status' => true,
      'id' => $this->insertID()
    ];
  }
  public function getList()
  {
    return $this->db->table('wait w')
      ->select("
                w.id,
                w.mount,
                w.date,
                w.time,
                c.name AS customer,
                u.name,
                COUNT(d.id) AS items
            ")
      ->join('customers c', 'c.id = w.customer', 'left')
      ->join('wait_details d', 'd.wait = w.id', 'left')
      ->join('users u', 'w.user = u.id', 'left')
      ->groupBy('w.id')
      ->orderBy('w.id', 'DESC')
      ->get()
      ->getResultArray();
  }

  public function validation($customer)
  {
    $data = $this->select(['id', 'mount'])
      ->where('customer', $customer)
      ->first();

    return $data ?: false;
  }

  public function update_wait_mount($wait, $mount)
  {
    return $this->update($wait, [
      'mount' => $mount
    ]);
  }
  public function wait_delete($wait)
  {
    return $this->where('id', $wait)->delete();
  }
}