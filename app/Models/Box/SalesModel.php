<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class SalesModel extends Model
{
  protected $table = 'sales';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'type',
    'date',
    'time',
    'number',
    'sucursal',
    'user',
    'total_price',
    'total_cost',
    'total_margin',
    'status',
    'session',
    'customer',
    'expedition_point',
    'invoice_type',
    'cash_received',
    'cash_change',
    'payment'
  ];
  public function getHistorySales($session, $limit = 50, $offset = 0)
  {
    return $this->db->table('sales s')
      ->select("
            s.id,
            s.time,
            s.total_price,
            s.cash_received,
            IF(s.cash_change < 0, 0, s.cash_change) AS cash_change,
            c.name AS customer_name,
            st.name AS sale_type,
            GROUP_CONCAT(pt.name SEPARATOR ', ') AS payment_name
        ")
      ->join('customers c', 'c.id = s.customer', 'left')
      ->join('sales_type st', 'st.id = s.type', 'left')
      ->join('sales_payments sp', 'sp.sales = s.id', 'left')
      ->join('payment_type pt', 'pt.id = sp.type', 'left')
      ->where('s.session', $session)
      ->groupBy('s.id')
      ->orderBy('s.id', 'DESC')
      ->limit($limit, $offset)
      ->get()
      ->getResultArray();
  }
  public function countHistorySales($session)
  {
    return $this->db->table('sales')
      ->where('session', $session)
      ->countAllResults();
  }

  public function getSales(array $filters = [])
  {
    $builder = $this->db->table('sales s');

    $builder->select([
      's.*',

      'c.ci AS customer_ci',
      'c.name AS customer_name',

      'u.name AS user_name',
      'su.name AS sucursal_name',
      'st.name AS status_name',

      'it.name AS invoice_type_name',
      'sty.name AS sales_type_name',

      'ep.code AS expedition_point_code',

      'sp.amount AS payment_amount',
      'pt.id AS payment_id',
      'pt.name AS payment_name',
    ]);

    $builder->join(
      'customers c',
      'c.id = s.customer',
      'left'
    );

    $builder->join(
      'users u',
      'u.id = s.user',
      'left'
    );

    $builder->join(
      'sucursals su',
      'su.id = s.sucursal',
      'left'
    );

    $builder->join(
      'status st',
      'st.id = s.status',
      'left'
    );

    $builder->join(
      'invoice_type it',
      'it.id = s.invoice_type',
      'left'
    );

    $builder->join(
      'sales_type sty',
      'sty.id = s.type',
      'left'
    );

    $builder->join(
      'expedition_point ep',
      'ep.id = s.expedition_point',
      'left'
    );

    $builder->join(
      'sales_payments sp',
      'sp.sales = s.id',
      'left'
    );

    $builder->join(
      'payment_type pt',
      'pt.id = sp.type',
      'left'
    );

    foreach ($filters as $field => $value) {
      if ($value === '' || $value === null) {
        continue;
      }

      switch ($field) {
        case 'session':
          $builder->where('s.session', $value);
          break;

        case 'customer':
          $builder->where('s.customer', $value);
          break;

        case 'user':
          $builder->where('s.user', $value);
          break;

        case 'sucursal':
          $builder->where('s.sucursal', $value);
          break;

        case 'payment':
          $builder->where('pt.id', $value);
          break;

        case 'status':
          $builder->where('s.status', $value);
          break;

        case 'date':
          $builder->where('s.date', $value);
          break;

        case 'date_from':
          $builder->where('s.date >=', $value);
          break;

        case 'date_to':
          $builder->where('s.date <=', $value);
          break;

        case 'id':
          $builder->where('s.id', $value);
          break;
      }
    }

    return $builder
      ->orderBy('s.id', 'DESC')
      ->get()
      ->getResultArray();
  }

  public function add_sales($data)
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
  public function edit_sales($values, $sale_id)
  {
    // Verificar que la venta exista
    $sale = $this->find($sale_id);

    if (!$sale) {
      return false;
    }

    // Actualizar la venta
    $updated = $this->update(
      $sale_id,
      $values
    );

    return $updated;
  }


  public function getInvoiceDatatable(array $post): array
  {
    $draw = (int) ($post['draw'] ?? 1);
    $start = max(0, (int) ($post['start'] ?? 0));

    $length = (int) ($post['length'] ?? 10);

    if ($length < 1) {
      $length = 10;
    }

    if ($length > 100) {
      $length = 100;
    }

    $search = trim(
      (string) ($post['search']['value'] ?? '')
    );

    // COLUMNAS PERMITIDAS PARA ORDENAR
    $orderColumns = [
      0 => 's.id',
      1 => 's.number',
      2 => 'su.name',
      3 => 's.total_price'
    ];

    $orderIndex = (int) (
      $post['order'][0]['column'] ?? 0
    );

    $orderColumn = $orderColumns[$orderIndex] ?? 's.id';

    $orderDir = strtolower(
      $post['order'][0]['dir'] ?? 'desc'
    );

    $orderDir = $orderDir === 'desc' ? 'ASC' : 'DESC';

    // FECHA INICIAL: HACE 2 DÍAS, INCLUYENDO HOY
    $fechaDesde = date('Y-m-d', strtotime('-2 days'));
    $fechaHasta = date('Y-m-d');

    // CONSULTA BASE: SOLO VENTAS DE LOS ÚLTIMOS 3 DÍAS
    $buildQuery = function () use ($fechaDesde, $fechaHasta) {
      return $this->db->table('sales s')
        ->select([
          's.id',
          's.number',
          's.sucursal',
          's.total_price',
          'su.name AS sucursal_name'
        ])
        ->join(
          'sucursals su',
          'su.id = s.sucursal',
          'left'
        )
        ->where('s.date >=', $fechaDesde)
        ->where('s.date <=', $fechaHasta)
        ->where('s.type', 1)
        ->where('s.invoice_type', 1);
    };

    // TOTAL DE VENTAS DE LOS ÚLTIMOS 3 DÍAS
    $recordsTotal = $buildQuery()->countAllResults();

    // CONSULTA CON BÚSQUEDA
    $builder = $buildQuery();

    if ($search !== '') {
      $builder->groupStart()
        ->like('s.id', $search)
        ->orLike('s.number', $search)
        ->orLike('su.name', $search)
        ->groupEnd();
    }

    // TOTAL FILTRADO
    $recordsFiltered = $search === ''
      ? $recordsTotal
      : (clone $builder)->countAllResults();

    // DATOS PAGINADOS
    $rows = $builder
      ->orderBy($orderColumn, $orderDir)
      ->limit($length, $start)
      ->get()
      ->getResultArray();

    return [
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $rows
    ];
  }

}