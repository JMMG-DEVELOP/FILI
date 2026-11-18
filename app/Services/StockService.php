<?php

namespace App\Services;

class StockService
{
  /**
   * Registrar movimiento en el historial de stock.
   *
   * Utiliza la misma conexión y transacción
   * recibida desde ProductModel.
   *
   * @param array $data
   * @param object $db
   * @return int|false
   */
  public function history_movements_register(array $data, $db)
  {
    // =====================================================
    // 1. VALIDAR DATOS OBLIGATORIOS
    // =====================================================

    $required = [
      'product',
      'movement',
      'quantity',
      'user',
      'sucursal'
    ];

    foreach ($required as $field) {

      if (!isset($data[$field]) || $data[$field] === '') {
        return false;
      }
    }

    // =====================================================
    // 2. PREPARAR REGISTRO
    // =====================================================

    $movement = [
      'product' => $data['product'],
      'movement' => (int) $data['movement'],
      'quantity' => $data['quantity'],
      'user' => (int) $data['user'],
      'sucursal' => (int) $data['sucursal'],
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
      'operation' => null
    ];

    // =====================================================
    // 3. INSERTAR MOVIMIENTO
    // =====================================================

    $inserted = $db->table('stock_movements')->insert($movement);

    if (!$inserted) {
      return false;
    }

    return $db->insertID();
  }
}
