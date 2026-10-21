<?php

namespace App\Models\Invoices;

use CodeIgniter\Model;

class InvoicesDatatableModel extends Model
{
  protected $table = 'invoices';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'number',
    'mount',
    'date',
    'time',
    'user',
    'customer',
    'status'
  ];

  /**
   * Obtener facturas para DataTables con procesamiento del servidor.
   */


  public function getDatatable(array $post): array
  {
    $db = \Config\Database::connect();

    $draw = (int) ($post['draw'] ?? 0);
    $start = max(0, (int) ($post['start'] ?? 0));
    $length = (int) ($post['length'] ?? 10);

    if ($length !== -1) {
      $length = max(1, min($length, 100));
    }

    $searchData = $post['search'] ?? [];

    $search = is_array($searchData)
      ? trim((string) ($searchData['value'] ?? ''))
      : '';

    $baseQuery = static function () use ($db) {
      return $db->table('invoices i')
        ->join('users u', 'u.id = i.user', 'left')
        ->join('customers c', 'c.id = i.customer', 'left')
        ->join('status st', 'st.id = i.status', 'left');
    };

    // Total de facturas.
    $recordsTotal = $baseQuery()->countAllResults();

    // Aplicar búsqueda.
    $filteredQuery = $baseQuery();

    if ($search !== '') {
      $filteredQuery->groupStart()
        ->like('i.id', $search)
        ->orLike('i.number', $search)
        ->orLike('i.mount', $search)
        ->orLike('i.date', $search)
        ->orLike('i.time', $search)
        ->orLike('c.name', $search)
        ->orLike('c.ci', $search)
        ->orLike('u.name', $search)
        ->orLike('st.name', $search)
        ->groupEnd();
    }

    // Contar los registros filtrados usando una consulta independiente.
    $countQuery = $baseQuery();

    if ($search !== '') {
      $countQuery->groupStart()
        ->like('i.id', $search)
        ->orLike('i.number', $search)
        ->orLike('i.mount', $search)
        ->orLike('i.date', $search)
        ->orLike('i.time', $search)
        ->orLike('c.name', $search)
        ->orLike('c.ci', $search)
        ->orLike('u.name', $search)
        ->orLike('st.name', $search)
        ->groupEnd();
    }

    $recordsFiltered = $countQuery->countAllResults();

    // Columnas permitidas para ordenar.
    $orderColumns = [
      0 => 'i.id',
      1 => 'i.number',
      2 => 'i.date',
      3 => 'i.time',
      4 => 'c.name',
      5 => 'c.ci',
      6 => 'u.name',
      7 => 'i.mount',
      8 => 'st.name'
    ];

    $orderData = $post['order'] ?? [];
    $columnIndex = (int) ($orderData[0]['column'] ?? 0);

    $column = $orderColumns[$columnIndex] ?? 'i.id';

    $direction = strtolower(
      (string) ($orderData[0]['dir'] ?? 'desc')
    ) === 'desc' ? 'ASC' : 'DESC';

    $query = $filteredQuery
      ->select([
        'i.id',
        'i.number',
        'i.mount',
        'i.date',
        'i.time',
        'i.user AS user_id',
        'i.customer AS customer_id',
        'i.status AS status_id',
        'u.name AS user_name',
        'c.name AS customer_name',
        'c.ci AS customer_ci',
        'st.name AS status_name'
      ])
      ->orderBy($column, $direction)
      ->orderBy('i.id', 'DESC');

    if ($length !== -1) {
      $query->limit($length, $start);
    }

    return [
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $query->get()->getResultArray()
    ];
  }


}
