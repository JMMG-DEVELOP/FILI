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
      'receipt' => $values['receipt'] ?? null,
      'procedure_other' => isset($values['procedure_other'])
        ? $this->normalize($values['procedure_other'])
        : []

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
  private $ivaMap = [
    '10' => 1, // IVA 10% → id 1
    '5' => 2, // IVA 5% → id 2
    '0' => 3  // Exenta → id 3
  ];

  function sales($values)
  {
    $payment = $values['payment'];
    $customer = $values['customer'];
    $point = $values['point'];

    return [
      'type' => $payment['sales'] ?? null,
      // 'payment' => $payment['payment'] ?? null,
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

  function sales_payment_cash($values, $sale_id)
  {
    return [
      'type' => $values['payment']['payment'] ?? null,
      'sales' => $sale_id,
      'amount' => $values['cart']['totals']['total_price'] ?? 0,

    ];
  }

  function sales_other_payment($values, $sale_id)
  {
    $amount = str_replace('.', '', $values['procedure_other']['value_payment_mount'] ?? 0);

    return [
      'type' => $values['procedure_other']['value_payment_type'] ?? null,
      'total' => $values['cart']['totals']['total_price'] ?? 0,
      'sales' => $sale_id,

    ];
  }


  function sales_details($values, $sale_id)
  {
    $details = [];

    foreach ($values['cart']['items'] as $item) {

      $cant = (float) $item['cant'];
      $price = (float) $item['unit_price'];
      $cost = (float) $item['unit_cost'];

      $margin = $price - $cost;

      // 🔹 MAP IVA
      $iva = (int) $item['iva'];
      $ivaType = $this->ivaMap[$iva] ?? 3;

      // 🔥 BASE IMPONIBLE + IVA UNITARIO
      if ($iva === 10) {
        $base = $price / 1.1;
        $unit_iva = $price - $base;
      } elseif ($iva === 5) {
        $base = $price / 1.05;
        $unit_iva = $price - $base;
      } else {
        $base = $price;
        $unit_iva = 0;
      }

      $details[] = [
        'product' => $item['code'],
        'cant' => $cant,

        'unit_price' => $price,
        'unit_cost' => $cost,

        'base_imponible' => round($base, 2),   // 🔥 NUEVO
        'unit_iva' => round($unit_iva, 2),

        'total_price' => (float) $item['total_price'],
        'total_cost' => (float) $item['total_cost'],

        'total_iva' => round($unit_iva * $cant, 2),

        'type_iva' => $ivaType,

        'margin' => $margin,
        'total_margin' => $margin * $cant,

        'sales' => (int) $sale_id
      ];
    }

    return $details;
  }

  function box_movements($values, $sale_id)
  {
    return [
      'type' => 1,
      'payment' => $values['payment']['payment'] ?? null,
      'mount' => $values['cart']['totals']['total_price'] ?? 0,
      'box' => session()->get('box'),
      'sales' => $sale_id,
    ];
  }

  function box_movements_other($values, $sale_id)
  {
    $amount = str_replace('.', '', $values['procedure_other']['value_payment_mount'] ?? 0);
    return [
      'type' => 1,
      'payment' => $values['procedure_other']['value_payment_type'] ?? null,
      'mount' => (float) $amount,
      'box' => session()->get('box'),
      'sales' => $sale_id,
    ];
  }
  public function stock_movements($values, $sale_id)
  {
    $details = [];

    foreach ($values['cart']['items'] as $item) {

      $details[] = [
        'product' => $item['code'],
        'quantity' => (float) $item['cant'],
        'movement' => 1,
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'sucursal' => $values['point']['select_sucursal'] ?? null,
        'user' => trim($values['point']['user_id'] ?? null),
        'operation' => $sale_id
      ];
    }

    return $details;


  }
  public function stock_update($values)
  {
    $details = [];

    foreach ($values['cart']['items'] as $item) {

      $details[] = [
        'product' => $item['code'],
        'stock' => (float) $item['cant'],
        'sucursal' => (int) $values['point']['select_sucursal'] ?? null,
      ];
    }

    return $details;
  }
  // Customer
  public function customers_credits_diferencce($values)
  {
    return [
      'amount' => abs($values['change']) ?? 0,
      'customer' => $values['customer']['customer_id'],
    ];
  }

  public function customer_credits_details($values, $customer_credits_id, $type)
  {
    return [
      'credit' => $customer_credits_id,
      'credit_type' => $type,
      'mount' => abs($values['change']) ?? 0,
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
    ];
  }

  public function credits_sales_details($customer_credits_details_id, $sales_details_id)
  {
    return [
      'credit_detail' => $customer_credits_details_id,
      'sales' => $sales_details_id,
    ];
  }
}