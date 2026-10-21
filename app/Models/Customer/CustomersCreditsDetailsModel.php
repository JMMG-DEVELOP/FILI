<?php

namespace App\Models\Customer;

use CodeIgniter\Model;

class CustomersCreditsDetailsModel extends Model
{
  protected $table = 'customer_credits_details';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'credit',
    'credit_type',
    'mount',
    'date',
    'time',
    'user'
  ];
  public function getAnoterDatatable(int $customerId, array $post): array
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

    /*
     * Consulta base:
     * - Relaciona los detalles con la cuenta de crédito.
     * - Filtra por el cliente recibido.
     * - Solo muestra credit_type = 3.
     */
    $baseQuery = static function () use ($db, $customerId) {
      return $db->table('customer_credits_details ccd')
        ->join(
          'customers_credits cc',
          'cc.id = ccd.credit',
          'inner'
        )
        ->join(
          'users u',
          'u.id = ccd.user',
          'left'
        )
        ->where('cc.customer', $customerId)
        ->where('ccd.credit_type', 3);
    };

    // Total de registros sin búsqueda.
    $recordsTotal = $baseQuery()->countAllResults();

    // Consulta filtrada.
    $filteredQuery = $baseQuery();

    if ($search !== '') {
      $filteredQuery->groupStart()
        ->like('ccd.id', $search)
        ->orLike('ccd.date', $search)
        ->orLike('ccd.time', $search)
        ->orLike('ccd.mount', $search)
        ->orLike('u.name', $search)
        ->groupEnd();
    }

    // Total después de aplicar la búsqueda.
    $recordsFiltered = (clone $filteredQuery)->countAllResults();

    // Columnas permitidas para ordenar.
    $orderColumns = [
      0 => 'ccd.id',
      1 => 'ccd.date',
      2 => 'ccd.time',
      3 => 'ccd.mount',
      4 => 'ccd.id',
      5 => 'u.name'
    ];

    $orderData = $post['order'] ?? [];

    $columnIndex = (int) ($orderData[0]['column'] ?? 0);

    $column = $orderColumns[$columnIndex] ?? 'ccd.id';

    $direction = strtolower(
      (string) ($orderData[0]['dir'] ?? 'desc')
    ) === 'des' ? 'ASC' : 'DESC';

    // Obtener los registros.
    $query = $filteredQuery
      ->select([
        'ccd.id',
        'ccd.date',
        'ccd.time',
        'ccd.mount AS amount',
        'u.name AS user_name',
        "'ANOTACIÓN EN CUENTA' AS detail"
      ])
      ->orderBy($column, $direction);

    if ($length !== -1) {
      $query->limit($length, $start);
    }

    $data = $query->get()->getResultArray();

    return [
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data
    ];
  }
  public function add_CustomersCreditsDetailsModel($values)
  {
    if (!$this->insert($values)) {
      return false;
    }

    return $this->getInsertID();
  }
  public function get_credit_detail($id)
  {
    return $this
      ->where('id', $id)
      ->first();
  }
}