<?php

namespace App\Libraries;

class InfoSales
{
  function formatter($values)
  {
    return [
      'payment' => $this->normalize($values['payment']),
      'customer' => $this->normalize($values['customer']),
      'point' => $this->normalize($values['point']),
      'cart' => $values['cart'] ?? [],
      'cash' => $values['cash'] ?? 0,
      'change' => $values['change'] ?? 0,
      'receipt' => $values['receipt'] ?? null
    ];
  }
  function normalize($array)
  {
    $result = [];

    foreach ($array as $item) {
      if (isset($item['name']) && isset($item['value'])) {
        $result[$item['name']] = trim($item['value']);
      }
    }

    return $result;
  }

  function sales($values)
  {
    $payment = $values['payment'];
    $customer = $values['customer'];
    $point = $values['point'];

    return [
      'type' => $payment['sales'] ?? null,

      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),

      'number' => $point['invoice_number'] ?? null,
      'sucursal' => $point['select_sucursal'] ?? null,
      'user' => trim($point['user_id'] ?? null),

      'total_price' => $values['cart']['totals']['total_price'] ?? 0,
      'total_cost' => $values['cart']['totals']['total_cost'] ?? 0,
      'total_margin' => ($values['cart']['totals']['total_price'] ?? 0)
        - ($values['cart']['totals']['total_cost'] ?? 0),

      'status' => 1,
      'session' => session()->get('session'),

      'customer' => $customer['customer_id'] ?? null,
      'expedition_point' => $point['expedition_point_id'] ?? null,

      'invoice_type' => $values['receipt'] ?? null,

      'cash_received' => $values['cash'] ?? 0,
      'cash_change' => $values['change'] ?? 0,
    ];
  }
}