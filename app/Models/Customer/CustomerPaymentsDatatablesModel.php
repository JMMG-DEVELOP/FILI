<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomerPaymentsDatatablesModel extends Model
{
  protected $table = 'customer_payments';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  public function getDatatable(int $customerId, array $post): array
  {
    $draw = (int) ($post['draw'] ?? 0);
    $start = max(0, (int) ($post['start'] ?? 0));
    $length = (int) ($post['length'] ?? 10);
    $length = $length === -1 ? -1 : max(1, min($length, 100));

    $searchData = $post['search'] ?? [];
    $search = is_array($searchData)
      ? trim((string) ($searchData['value'] ?? ''))
      : '';

    $baseQuery = function () use ($customerId) {
      return $this->db->table('customer_payments cp')
        ->join(
          'customers_credits cc',
          'cc.id = cp.customer_credit',
          'inner'
        )
        ->join(
          'users u',
          'u.id = cp.user',
          'left'
        )
        ->where('cc.customer', $customerId);
    };

    // Total de pagos del cliente, sin aplicar búsqueda.
    $recordsTotal = $baseQuery()
      ->countAllResults();

    // Consulta con búsqueda.
    $filteredQuery = $baseQuery();

    if ($search !== '') {
      $filteredQuery
        ->groupStart()
        ->like('cp.id', $search)
        ->orLike('cp.date', $search)
        ->orLike('cp.time', $search)
        ->orLike('cp.amount', $search)
        ->orLike('cp.detail', $search)
        ->orLike('u.name', $search)
        ->groupEnd();
    }

    // Total después de aplicar la búsqueda.
    $recordsFiltered = (clone $filteredQuery)
      ->countAllResults();

    // Columnas permitidas para ordenar.
    $orderColumns = [
      0 => 'cp.id',
      1 => 'cp.date',
      2 => 'cp.time',
      3 => 'cp.amount',
      4 => 'cp.detail',
      5 => 'u.name',
    ];

    $orderData = $post['order'] ?? [];
    $columnIndex = (int) ($orderData[0]['column'] ?? 0);
    $column = $orderColumns[$columnIndex] ?? 'cp.id';

    $direction = strtolower(
      $orderData[0]['dir'] ?? 'desc'
    ) === 'desc' ? 'ASC' : 'DESC';

    $query = $filteredQuery
      ->select([
        'cp.id',
        'cp.customer_credit',
        'cp.date',
        'cp.time',
        'cp.amount',
        'cp.detail',
        'u.name AS user_name',
      ])
      ->orderBy($column, $direction);

    // Paginación.
    if ($length !== -1) {
      $query->limit($length, $start);
    }

    $data = $query->get()->getResultArray();

    return [
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data,
    ];
  }
}
