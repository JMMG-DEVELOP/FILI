<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class BoxMovementModel extends Model
{
  protected $table = 'box_movement';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'type',
    'payment',
    'mount',
    'box',
    'sales'
  ];

  public function add_box_movement($values)
  {
    return $this->insert($values);
  }
  public function getMovements(array $filters = [], ?int $limit = null, ?int $offset = null)
  {
    $builder = $this->db->table('box_movement bm');

    $builder->select("
        bm.*,

        s.time,
        s.total_price,
        s.cash_received,
        IF(s.cash_change < 0, 0, s.cash_change) AS cash_change,

        c.name AS customer_name,
        st.name AS sale_type,
        pt.name AS payment_name,

        s.number AS sales_number,
        bmt.name AS movement_type_name,
        u.name AS user_name
    ");

    $builder->join(
      'box_movement_type bmt',
      'bmt.id = bm.type',
      'left'
    );

    $builder->join(
      'payment_type pt',
      'pt.id = bm.payment',
      'left'
    );

    $builder->join(
      'sales s',
      's.id = bm.sales',
      'left'
    );

    $builder->join(
      'customers c',
      'c.id = s.customer',
      'left'
    );

    $builder->join(
      'sales_type st',
      'st.id = s.type',
      'left'
    );

    $builder->join(
      'box b',
      'b.id = bm.box',
      'left'
    );

    $builder->join(
      'users u',
      'u.id = b.user',
      'left'
    );

    // Filtros dinámicos
    foreach ($filters as $field => $value) {

      if ($value === '' || $value === null) {
        continue;
      }

      switch ($field) {

        case 'id':
          $builder->where('bm.id', $value);
          break;

        case 'box':
          $builder->where('bm.box', $value);
          break;

        case 'type':
          $builder->where('bm.type', $value);
          break;

        case 'type_not':
          $builder->where('bm.type !=', $value);
          break;

        case 'payment':
          $builder->where('bm.payment', $value);
          break;

        case 'payment_not':
          $builder->where('bm.payment !=', $value);
          break;

        case 'sales':
          $builder->where('bm.sales', $value);
          break;

        case 'user':
          $builder->where('b.user', $value);
          break;

        case 'mount_min':
          $builder->where('bm.mount >=', $value);
          break;

        case 'mount_max':
          $builder->where('bm.mount <=', $value);
          break;
      }
    }

    $builder->orderBy('bm.id', 'DESC');

    if ($limit !== null) {
      $builder->limit($limit, $offset);
    }

    return $builder->get()->getResultArray();
  }
  public function countMovements(array $filters = [])
  {
    $builder = $this->db->table('box_movement bm');

    $builder->join('box b', 'b.id = bm.box', 'left');

    foreach ($filters as $field => $value) {

      if ($value === '' || $value === null) {
        continue;
      }

      switch ($field) {

        case 'id':
          $builder->where('bm.id', $value);
          break;

        case 'box':
          $builder->where('bm.box', $value);
          break;

        case 'type':
          $builder->where('bm.type', $value);
          break;

        case 'type_not':
          $builder->where('bm.type !=', $value);
          break;

        case 'payment':
          $builder->where('bm.payment', $value);
          break;

        case 'payment_not':
          $builder->where('bm.payment !=', $value);
          break;

        case 'sales':
          $builder->where('bm.sales', $value);
          break;

        case 'user':
          $builder->where('b.user', $value);
          break;

        case 'mount_min':
          $builder->where('bm.mount >=', $value);
          break;

        case 'mount_max':
          $builder->where('bm.mount <=', $value);
          break;
      }
    }

    return $builder->countAllResults();
  }
  // public function getMovements(array $filters = [])
  // {
  //   $builder = $this->db->table('box_movement bm');

  //   $builder->select([
  //     'bm.*',
  //     'bmt.name AS movement_type_name',
  //     'pt.name AS payment_name',
  //     's.number AS sales_number',
  //     's.total_price AS sales_total',
  //     'u.name AS user_name'
  //   ]);

  //   $builder->join(
  //     'box_movement_type bmt',
  //     'bmt.id = bm.type',
  //     'left'
  //   );

  //   $builder->join(
  //     'payment_type pt',
  //     'pt.id = bm.payment',
  //     'left'
  //   );

  //   $builder->join(
  //     'sales s',
  //     's.id = bm.sales',
  //     'left'
  //   );

  //   $builder->join(
  //     'box b',
  //     'b.id = bm.box',
  //     'left'
  //   );

  //   $builder->join(
  //     'users u',
  //     'u.id = b.user',
  //     'left'
  //   );

  //   // filtros dinámicos
  //   foreach ($filters as $field => $value) {
  //     if ($value === '' || $value === null) {
  //       continue;
  //     }

  //     switch ($field) {
  //       case 'id':
  //         $builder->where('bm.id', $value);
  //         break;

  //       case 'box':
  //         $builder->where('bm.box', $value);
  //         break;

  //       case 'type':
  //         $builder->where('bm.type', $value);
  //         break;

  //       case 'type_not':
  //         $builder->where('bm.type !=', $value);
  //         break;

  //       case 'payment':
  //         $builder->where('bm.payment', $value);
  //         break;

  //       case 'payment_not':
  //         $builder->where('bm.payment !=', $value);
  //         break;

  //       case 'sales':
  //         $builder->where('bm.sales', $value);
  //         break;

  //       case 'user':
  //         $builder->where('b.user', $value);
  //         break;

  //       case 'mount_min':
  //         $builder->where('bm.mount >=', $value);
  //         break;

  //       case 'mount_max':
  //         $builder->where('bm.mount <=', $value);
  //         break;
  //     }
  //   }

  //   return $builder
  //     ->orderBy('bm.id', 'DESC')
  //     ->get()
  //     ->getResultArray();
  // }
}
