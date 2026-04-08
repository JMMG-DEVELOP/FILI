<?php

namespace App\Services;
class SalesService
{
  public function createSale($data)
  {

  }

  public function createSaleDetails($saleId, $cart)
  {

  }

  public function createPayment($saleId, $data)
  {
    $db = \Config\Database::connect();

    $db->table('sales_payments')->insert([
      'type' => $data['payment']['payment'], // efectivo
      'amount' => $this->getTotal($data['cart']),
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
      'sales' => $saleId
    ]);
  }
  public function createBoxMovement($saleId, $data)
  {
    $db = \Config\Database::connect();

    $total = $this->getTotal($data['cart']);
    $cash = (float) $data['cash'];
    $change = (float) $data['change'];

    $db->table('box_movement')->insert([
      'type' => 1, // ingreso
      'payment' => $data['payment']['payment'],
      'payment_mount' => $total,
      'payment_received' => $cash,
      'payment_change' => $change,
      'box' => session('box'),
      'sales' => $saleId,
      'quantity' => $data['cant']
    ]);
  }

  public function discountStock($cart)
  {
    $db = \Config\Database::connect();

    foreach ($cart as $item) {

      $db->table('products_stock')
        ->set('stock', 'stock - ' . (float) $item['cant'], false)
        ->where('product', $item['product_id'])
        ->where('sucursal', session('sucursal'))
        ->update();
    }
  }
  public function generateNumber($data)
  {
    $db = \Config\Database::connect();

    $expeditionPointId = $data['point']['select_sucursal'];

    // obtener punto de expedición
    $point = $db->table('expedition_point')
      ->where('id', $expeditionPointId)
      ->get()
      ->getRow();

    if (!$point) {
      throw new \Exception('Punto de expedición no encontrado');
    }

    // obtener secuencia
    $seq = $db->table('document_sequence')
      ->where('expedition_point', $expeditionPointId)
      ->get()
      ->getRow();

    if (!$seq) {
      throw new \Exception('Secuencia no encontrada');
    }

    $newNumber = (int) $seq->last_number + 1;

    // actualizar secuencia
    $db->table('document_sequence')
      ->where('expedition_point', $expeditionPointId)
      ->update([
        'last_number' => $newNumber
      ]);

    // formatear partes
    $sucursal = str_pad($point->sucursal, 3, '0', STR_PAD_LEFT);
    $exp = str_pad($point->code, 3, '0', STR_PAD_LEFT);
    $number = str_pad($newNumber, 7, '0', STR_PAD_LEFT);

    return "{$sucursal}-{$exp}-{$number}";
  }

}
?>