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
    'mount',
    'box',
    'sales',
    'sales_type',
    'sales_payment'
  ];

  public function add_box_movement($values)
  {
    return $this->insert($values);
  }

  public function getMovements(
    array $filters = [],
    ?int $limit = null,
    ?int $offset = null
  ) {
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

        u.name AS user_name,

        sp.number AS operation_number,

        pd.name AS device_name

    ");

    /*
     * Tipo de movimiento
     */
    $builder->join(
      'box_movement_type bmt',
      'bmt.id = bm.type',
      'left'
    );

    /*
     * Medio de pago
     *
     * bm.sales_payment:
     * 1 = Efectivo
     * 2 = QR
     * 3 = Transferencia
     * 4 = Tarjeta
     */
    $builder->join(
      'payment_type pt',
      'pt.id = bm.sales_payment',
      'left'
    );

    /*
     * Venta
     */
    $builder->join(
      'sales s',
      's.id = bm.sales',
      'left'
    );

    /*
     * Cliente
     */
    $builder->join(
      'customers c',
      'c.id = s.customer',
      'left'
    );

    /*
     * Tipo de venta
     *
     * sales_type:
     * 1 = Contado
     * 2 = Crédito
     */
    $builder->join(
      'sales_type st',
      'st.id = bm.sales_type',
      'left'
    );

    /*
     * Caja
     */
    $builder->join(
      'box b',
      'b.id = bm.box',
      'left'
    );

    /*
     * Usuario
     */
    $builder->join(
      'users u',
      'u.id = b.user',
      'left'
    );

    /*
     * Datos del pago
     *
     * sales_payments.sales = id de la venta
     */
    $builder->join(
      'sales_payments sp',
      'sp.sales = bm.sales AND sp.type = bm.sales_payment',
      'left'
    );

    /*
     * Dispositivo de pago
     *
     * sp.device = payment_device.id
     */
    $builder->join(
      'payment_device pd',
      'pd.id = sp.device',
      'left'
    );


    /*
     * FILTROS
     */
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
          $builder->where('bm.sales_payment', $value);
          break;

        case 'payment_not':
          $builder->where('bm.sales_payment !=', $value);
          break;
        case 'sales_null':
          $builder->where('bm.sales IS NULL', null, false);
          break;

        case 'sales':
          $builder->where('bm.sales', $value);
          break;

        case 'sales_type':
          $builder->where('bm.sales_type', $value);
          break;

        case 'type_not_in':
          $builder->whereNotIn('bm.type', $value);
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

    $builder->join(
      'box b',
      'b.id = bm.box',
      'left'
    );

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

        case 'type_not_in':
          $builder->whereNotIn('bm.type', $value);
          break;

        case 'payment':
          $builder->where('bm.sales_payment', $value);
          break;

        case 'payment_not':
          $builder->where('bm.sales_payment !=', $value);
          break;

        case 'sales':
          $builder->where('bm.sales', $value);
          break;

        case 'sales_null':
          $builder->where('bm.sales IS NULL', null, false);
          break;

        case 'sales_type':
          $builder->where('bm.sales_type', $value);
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
}