<?php

namespace App\Models\Box;

use CodeIgniter\Model;

class InvoiceSequenceModel extends Model
{
  protected $table = 'invoice_sequence';
  protected $primaryKey = 'id';
  protected $returnType = 'array';

  protected $allowedFields = [
    'last_number',
    'expedition_point'
  ];

  public function update_last_number($id)
  {
    $row = $this->find($id);

    if (!$row)
      return false;

    // 🔹 obtener número actual
    $current = (int) $row['last_number'];

    // 🔹 incrementar
    $next = $current + 1;

    // 🔹 formatear con ceros (7 dígitos)
    $formatted = str_pad($next, 7, '0', STR_PAD_LEFT);

    // 🔹 actualizar
    $this->update($id, [
      'last_number' => $formatted
    ]);

    return $formatted;
  }

}