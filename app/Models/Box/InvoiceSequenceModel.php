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


  public function update_last_number_from_document(string $documentNumber): bool
  {
    $documentNumber = trim($documentNumber);
    $parts = preg_split('/\s+/', $documentNumber);

    // Validar el formato: 001 002 0000049.
    if (
      count($parts) !== 3 ||
      !ctype_digit($parts[0]) ||
      !ctype_digit($parts[1]) ||
      !ctype_digit($parts[2])
    ) {
      return false;
    }

    $sucursal = (int) $parts[0];
    $expeditionPointCode = $parts[1];
    $number = $parts[2];

    $db = $this->db;

    // Buscar el punto de expedición por código y sucursal.
    $expeditionPoint = $db->table('expedition_point')
      ->select('id')
      ->where('code', $expeditionPointCode)
      ->where('sucursal', $sucursal)
      ->get()
      ->getRowArray();

    if (!$expeditionPoint) {
      log_message(
        'error',
        'No se encontró el punto de expedición. ' .
        'Sucursal: ' . $sucursal .
        ', Código: ' . $expeditionPointCode
      );

      return false;
    }

    $expeditionPointId = (int) $expeditionPoint['id'];

    // Actualizar la secuencia correspondiente al punto de expedición.
    $sequence = $this->where(
      'expedition_point',
      $expeditionPointId
    )->first();

    if (!$sequence) {
      log_message(
        'error',
        'No existe una secuencia para el punto de expedición ID: ' .
        $expeditionPointId
      );

      return false;
    }

    return $this->update($sequence['id'], [
      'last_number' => $number
    ]);
  }

}