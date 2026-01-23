<?php

namespace App\Libraries;


class Products
{
  public function prepare_table_product($var)
  {
    return $product = [
      'code' => $var['code'],
      'description' => $var['description'],
      'status' => $var['status'],
      'section' => $var['section'],
      'brand' => $var['brand'],
      'price' => $var['price'],
      'cost' => $var['cost'],
      'sales' => $var['sales'],
      'iva' => $var['iva'],
    ];
  }

  public function prepare_table_prices($var)
  {
    return $price = [
      'price_one' => $var['price_one'],
      'price_two' => $var['price_two'],
      'price_three' => $var['price_three'],
      'cant_one' => $var['cant_one'],
      'cant_two' => $var['cant_two'],
      'cant_three' => $var['cant_three'],
      'product' => $var['code'],
    ];
  }
  public function prepare_table_cost($var)
  {
    return $price = [
      'cost' => $var['cost'],
      'other_cost' => $var['other_cost'],
      'product' => $var['code'],
    ];
  }

}