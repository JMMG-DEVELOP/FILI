<?php

namespace App\Libraries;

class ProductsFormatter
{
  public function tables_formatters(array $values): array
  {
    return [
      'products' => $this->prepare_table_product($values),
      'prices' => $this->prepare_table_prices($values),
      'costs' => $this->prepare_table_cost($values),
      'stock' => $this->prepare_table_stock($values),
    ];
  }

  // ============================================================
  // LIMPIAR GUARANÍES
  // ============================================================

  public function cleanMoney($value): int
  {
    if ($value === null || $value === '') {
      return 0;
    }

    return (int) preg_replace('/\D/', '', (string) $value);
  }

  // ============================================================
  // PRODUCTO
  // ============================================================

  public function prepare_table_product(array $var): array
  {
    return [
      'code' => trim($var['code'] ?? ''),
      'description' => trim($var['description'] ?? ''),
      'status' => 1,
      'section' => (int) ($var['section'] ?? 0),
      'brand' => (int) ($var['brand'] ?? 0),
      'sales' => (int) ($var['sales'] ?? 0),
      'iva' => (int) ($var['iva'] ?? 0),
    ];
  }

  // ============================================================
  // PRECIOS
  // ============================================================

  public function prepare_table_prices(array $var): array
  {
    return [
      'product' => trim($var['code'] ?? ''),
      'price_one' => $this->cleanMoney($var['price_one'] ?? 0),
      'price_two' => $this->cleanMoney($var['price_two'] ?? 0),
      'price_three' => $this->cleanMoney($var['price_three'] ?? 0),
      'cant_two' => (int) ($var['cant_two'] ?? 0),
      'cant_three' => (int) ($var['cant_three'] ?? 0),
    ];
  }

  // ============================================================
  // COSTO
  // ============================================================

  public function prepare_table_cost(array $var): array
  {
    return [
      'product' => trim($var['code'] ?? ''),
      'cost' => $this->cleanMoney($var['cost'] ?? 0),
    ];
  }

  // ============================================================
  // STOCK INICIAL POR SUCURSAL
  // Acepta edit_stock[1], edit_stock[2], etc.
  // ============================================================



  public function prepare_table_stock(array $var): array
  {
    $stocks = [];

    $stockInput = $var['edit_stock'] ?? [];

    if (!is_array($stockInput)) {
      return $stocks;
    }

    foreach ($stockInput as $sucursal => $quantity) {

      if ($quantity === '' || $quantity === null) {
        continue;
      }

      // Permitir números positivos y negativos
      $quantity = str_replace(',', '.', trim((string) $quantity));

      if (!is_numeric($quantity)) {
        throw new \InvalidArgumentException(
          'Cantidad de stock inválida'
        );
      }

      $quantity = (float) $quantity;

      if ($quantity == 0) {
        continue;
      }

      $stocks[] = [
        'product' => $var['code'],
        'sucursal' => (int) $sucursal,
        'stock' => $quantity,
      ];
    }

    return $stocks;
  }

}
