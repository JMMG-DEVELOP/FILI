<?php

namespace App\Libraries;

class ProductsFormatter
{
  /**
   * Limpia valores monetarios:
   * - null / vacío => 0
   * - elimina separadores de miles
   * - devuelve int
   */
  public function tables_formatters($values)
  {
    return [
      'products' => $this->prepare_table_product($values),
      'prices' => $this->prepare_table_prices($values),
      'costs' => $this->prepare_table_cost($values),
      'stock' => $this->prepare_table_stock($values),
    ];
  }
  public function cleanMoney($value): int
  {
    if ($value === null || $value === '') {
      return 0;
    }

    // Elimina todo lo que no sea número
    $value = preg_replace('/[^0-9]/', '', (string) $value);

    return (int) $value;
  }

  public function prepare_table_product(array $var): array
  {
    return [
      'code' => $var['code'],
      'description' => $var['description'],
      'status' => 1,
      'section' => $var['section'],
      'brand' => $var['brand'],
      'sales' => $var['sales'],
      'iva' => $var['iva'],
    ];
  }

  public function prepare_table_prices(array $var): array
  {
    return [
      'price_one' => $this->cleanMoney($var['price_one'] ?? 0),
      'price_two' => $this->cleanMoney($var['price_two'] ?? 0),
      'price_card' => $this->cleanMoney($var['price_card'] ?? 0),

      // 'cant_one' => (int) ($var['cant_one'] ?? 0),
      'cant_two' => (int) ($var['cant_two'] ?? 0),
      // 'cant_three' => (int) ($var['cant_three'] ?? 0),

      'product' => $var['code'],
    ];
  }

  public function prepare_table_cost(array $var): array
  {
    return [
      'cost' => $this->cleanMoney($var['cost'] ?? 0),
      'other_cost' => $this->cleanMoney($var['other_cost'] ?? 0),
      'product' => $var['code'],
    ];
  }

  public function prepare_table_stock(array $var): array
  {
    $stocks = [];

    foreach ($var as $key => $value) {

      if (preg_match('/^edit_stock_(\d+)$/', $key, $match)) {

        $branchId = (int) $match[1];
        $stock = (int) $value;

        $stocks[] = [
          'product' => $var['code'],
          'sucursal' => $branchId,
          'stock' => $stock,
        ];
      }
    }

    return $stocks;
  }

}
