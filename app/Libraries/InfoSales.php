<?php

namespace App\Libraries;

class InfoSales
{
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
        'base_imponible' => round($base, 2),
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

  public function sales_iva($values, $sale_id)
  {
    $iva = $values['cart']['iva'] ?? [];

    return [
      [
        'type' => 1, // IVA 10%
        'mount' => $iva['iva_10'] ?? 0,
        'sales' => $sale_id
      ],
      [
        'type' => 2, // IVA 5%
        'mount' => $iva['iva_5'] ?? 0,
        'sales' => $sale_id
      ],
      [
        'type' => 3, // Exenta
        'mount' => $iva['exenta'] ?? 0,
        'sales' => $sale_id
      ]
    ];
  }

  // ***** STOCK 
  public function products_stock($values)
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
  public function devolution_Stock($values)
  {
    $items = [];

    foreach ($values['cart']['items'] as $item) {

      $items[] = [
        'product' => $item['code'], // products_stock.product guarda el código
        'stock' => $item['cant'],
        'sucursal' => $values['point']['select_sucursal']
      ];
    }

    return $items;
  }
  public function stock_movements($values, $movement, $sale_id)
  {
    $details = [];

    foreach ($values['cart']['items'] as $item) {

      $details[] = [
        'product' => $item['code'],
        'quantity' => (float) $item['cant'],
        'movement' => $movement,
        'date' => date('Y-m-d'),
        'time' => date('H:i:s'),
        'sucursal' => $values['point']['select_sucursal'] ?? null,
        'user' => trim($values['point']['user_id'] ?? null),
        'operation' => $sale_id
      ];
    }

    return $details;
  }
  function box_movements($values, $sale_id, $type)
  {
    return [
      'type' => $type,
      'mount' => $values['cart']['totals']['total_price'] ?? 0,
      'box' => session()->get('box'),
      'sales' => $sale_id,
      'sales_type' => $values['payment']['sales'] ?? null,
      'sales_payment' => $values['payment']['payment'] ?? null

    ];
  }
  function box_movements_cash_credit_cash($values, $sale_id, $type)
  {
    return [
      'type' => $type,
      'mount' => $values['cash'] ?? 0,
      'box' => session()->get('box'),
      'sales' => $sale_id,
      'sales_type' => $values['payment']['sales'] ?? null,
      'sales_payment' => $values['payment']['payment'] ?? null

    ];
  }
  function box_movements_cash_digits_digits($values, $sale_id, $type)
  {
    return [
      'type' => $type,
      'mount' => $values['cash_digist_payment']['cash_digits_mount'] ?? 0,
      'box' => session()->get('box'),
      'sales' => $sale_id,
      'sales_type' => $values['payment']['sales'] ?? null,
      'sales_payment' => $values['cash_digist_payment']['cash_digits_payment_type'] ?? null

    ];
  }
  function box_movements_cash_credit_credit($values, $sale_id, $type)
  {
    return [
      'type' => $type,
      'mount' => abs($values['cash_credit_payment']['cash_credit_mount']) ?? 0,
      'box' => session()->get('box'),
      'sales' => $sale_id,
      'sales_type' => 2,
      'sales_payment' => $values['payment']['payment'] ?? null

    ];
  }
  // CASH CREDIT
  public function multi_payment_cash($values, $sale_id)
  {
    return [
      'type' => $values['payment']['payment'] ?? null,
      'amount' => $values['cash'] ?? 0,
      'sales' => $sale_id,
      'device' => 1
    ];

  }

  public function multi_payment_credit($values)
  {
    return [
      'amount' => abs($values['cash_credit_payment']['cash_credit_mount']) ?? 0,
      'customer' => $values['customer']['customer_id'],

    ];

  }
  public function multi_payment_credit_detail($values, $customer_credits_id, $type)
  {
    return [
      'credit' => $customer_credits_id,
      'credit_type' => $type,
      'mount' => abs($values['cash_credit_payment']['cash_credit_mount']) ?? 0,
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
    ];
  }
  public function multi_payment_digist($values, $sale_id)
  {
    return [
      'type' => $values['cash_digist_payment']['cash_digits_payment_type'] ?? null,
      'amount' => $values['cash_digist_payment']['cash_digits_mount'] ?? 0,
      'sales' => $sale_id,
      'device' => $values['cash_digist_payment']['cash_digits_device_payment'] ?? 1,
    ];

  }

  // PAGO CASH
  function payment_cash($values, $sale_id)
  {

    return [
      'type' => $values['payment']['payment'] ?? null,
      'amount' => $values['cart']['totals']['total_price'] ?? 0,
      'sales' => $sale_id,
      'device' => 1
    ];
  }

  function payment_digist($values, $sale_id)
  {

    return [
      'type' => $values['payment']['payment'] ?? null,
      'amount' => $values['cart']['totals']['total_price'] ?? 0,
      'sales' => $sale_id,
      'device' => $values['digist_payment']['device_payment'],
      'number' => $values['digist_payment']['payment_device_operation_number']

    ];
  }
  // CREDIT
  public function payment_credit($values)
  {
    return [
      'amount' => abs($values['cart']['totals']['total_price']) ?? 0,
      'customer' => $values['customer']['customer_id'],

    ];
  }

  public function payment_credit_detail($values, $customer_credits_id, $type)
  {
    return [
      'credit' => $customer_credits_id,
      'credit_type' => $type,
      'mount' => abs($values['cart']['totals']['total_price'] ?? 0),
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
    ];
  }

  public function payment_credit_sales_detail($customer_credits_details_id, $sales_details_id)
  {
    return [
      'credit_detail' => $customer_credits_details_id,
      'sales' => $sales_details_id,
    ];
  }



}