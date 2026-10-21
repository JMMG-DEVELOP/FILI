<?php

namespace App\Models\Invoices;

use CodeIgniter\Model;

class InvoicesModel extends Model
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
    'sucursal',
    'status'
  ];


  public function get_card(): array
  {
    $db = \Config\Database::connect();

    $today = date('Y-m-d');
    $yearStart = date('Y-01-01');
    $monthStart = date('Y-m-01');

    // RESUMEN GENERAL
    $generalRow = $db->table('invoices')
      ->select("
            COALESCE(SUM(mount), 0) AS total,
            COUNT(id) AS quantity,

            COALESCE(SUM(
                CASE WHEN date = '{$today}'
                THEN mount ELSE 0 END
            ), 0) AS today_total,

            COALESCE(SUM(
                CASE WHEN date = '{$today}'
                THEN 1 ELSE 0 END
            ), 0) AS today_quantity,

            COALESCE(SUM(
                CASE WHEN date >= '{$yearStart}'
                AND date <= '{$today}'
                THEN mount ELSE 0 END
            ), 0) AS year_total,

            COALESCE(SUM(
                CASE WHEN date >= '{$yearStart}'
                AND date <= '{$today}'
                THEN 1 ELSE 0 END
            ), 0) AS year_quantity,

            COALESCE(SUM(
                CASE WHEN date >= '{$monthStart}'
                AND date <= '{$today}'
                THEN mount ELSE 0 END
            ), 0) AS month_total,

            COALESCE(SUM(
                CASE WHEN date >= '{$monthStart}'
                AND date <= '{$today}'
                THEN 1 ELSE 0 END
            ), 0) AS month_quantity
        ")
      ->where('status', 1)
      ->get()
      ->getRowArray() ?? [];

    // RESUMEN POR SUCURSAL
    // Se muestran todas las sucursales, incluso sin facturas.
    $branchRows = $db->table('sucursals s')
      ->select("
            s.id AS sucursal_id,
            s.name AS sucursal_name,

            COALESCE(SUM(i.mount), 0) AS total,
            COUNT(i.id) AS quantity,

            COALESCE(SUM(
                CASE WHEN i.date = '{$today}'
                THEN i.mount ELSE 0 END
            ), 0) AS today_total,

            COALESCE(SUM(
                CASE WHEN i.date = '{$today}'
                THEN 1 ELSE 0 END
            ), 0) AS today_quantity,

            COALESCE(SUM(
                CASE WHEN i.date >= '{$yearStart}'
                AND i.date <= '{$today}'
                THEN i.mount ELSE 0 END
            ), 0) AS year_total,

            COALESCE(SUM(
                CASE WHEN i.date >= '{$yearStart}'
                AND i.date <= '{$today}'
                THEN 1 ELSE 0 END
            ), 0) AS year_quantity,

            COALESCE(SUM(
                CASE WHEN i.date >= '{$monthStart}'
                AND i.date <= '{$today}'
                THEN i.mount ELSE 0 END
            ), 0) AS month_total,

            COALESCE(SUM(
                CASE WHEN i.date >= '{$monthStart}'
                AND i.date <= '{$today}'
                THEN 1 ELSE 0 END
            ), 0) AS month_quantity
        ")
      ->join(
        'invoices i',
        'i.sucursal = s.id AND i.status = 1',
        'inner'
      )
      ->groupBy('s.id, s.name')
      ->orderBy('s.name', 'DESC')
      ->get()
      ->getResultArray();

    // FORMATEAR TOTAL Y CANTIDAD
    $formatSummary = static function ($total, $quantity): array {
      return [
        'total' => (float) ($total ?? 0),
        'quantity' => (int) ($quantity ?? 0)
      ];
    };

    // FORMATEAR DATOS POR SUCURSAL Y PERÍODO
    $formatBranches = static function (array $rows, string $totalKey, string $quantityKey) use ($formatSummary): array {
      return array_map(
        static function (array $row) use ($totalKey, $quantityKey, $formatSummary): array {
          return [
            'sucursal_id' => (int) $row['sucursal_id'],
            'sucursal_name' => $row['sucursal_name'],
            ...$formatSummary(
              $row[$totalKey] ?? 0,
              $row[$quantityKey] ?? 0
            )
          ];
        },
        $rows
      );
    };

    return [
      'general' => $formatSummary(
        $generalRow['total'] ?? 0,
        $generalRow['quantity'] ?? 0
      ),

      'today' => $formatSummary(
        $generalRow['today_total'] ?? 0,
        $generalRow['today_quantity'] ?? 0
      ),

      'year' => $formatSummary(
        $generalRow['year_total'] ?? 0,
        $generalRow['year_quantity'] ?? 0
      ),

      'month' => $formatSummary(
        $generalRow['month_total'] ?? 0,
        $generalRow['month_quantity'] ?? 0
      ),

      'branches' => $formatBranches(
        $branchRows,
        'total',
        'quantity'
      ),

      'branches_today' => $formatBranches(
        $branchRows,
        'today_total',
        'today_quantity'
      ),

      'branches_year' => $formatBranches(
        $branchRows,
        'year_total',
        'year_quantity'
      ),

      'branches_month' => $formatBranches(
        $branchRows,
        'month_total',
        'month_quantity'
      )
    ];
  }

  public function add($values)
  {
    if (empty($values)) {
      log_message(
        'warning',
        'INVOICES MODEL: No se recibieron datos para guardar la factura.'
      );

      return false;
    }

    try {
      $id = $this->insert($values, true);

      if ($id !== false && $id !== null) {
        return (int) $id;
      }

      log_message(
        'error',
        'INVOICES MODEL: Error al guardar la factura. ' .
        json_encode([
          'errors' => $this->errors(),
          'db_error' => $this->db->error(),
          'last_query' => (string) $this->db->getLastQuery()
        ], JSON_UNESCAPED_UNICODE)
      );

      return false;

    } catch (\Throwable $e) {

      log_message(
        'error',
        'INVOICES MODEL: Excepción al guardar la factura. ' .
        $e->getMessage() .
        ' | Archivo: ' . $e->getFile() .
        ' | Línea: ' . $e->getLine()
      );

      return false;
    }
  }



}