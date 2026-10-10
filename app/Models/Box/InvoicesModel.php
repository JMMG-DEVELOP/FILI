<?php

namespace App\Models\Box;

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
    'customer'
  ];


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