<?php
namespace App\Libraries;

class InfoWait
{
  public function wait($values)
  {
    return [
      'customer' => $values['customer']['customer_id'] ?? 1,
      'date' => date('Y-m-d'),
      'time' => date('H:i:s'),
      'user' => session()->get('id'),
      'mount' => $values['cart']['totals']['total_price'] ?? 0,
    ];
  }

  public function wait_details($values, $wait_id)
  {
    $details = [];

    foreach ($values['cart']['items'] as $item) {

      $details[] = [

        'id' => $item['product_id'],
        'cant' => (float) $item['cant'],
        'wait' => $wait_id,
        'price_one' => (float) $item['unit_price'],
        'price_two' => (float) $item['price_two'],
        'cant_two' => (float) $item['cant_two'],
        'cost' => (float) $item['unit_cost'],
        'iva' => (int) $item['iva'],
        'code' => $item['code'],
        'description' => $item['description']
      ];
    }

    return $details;
  }
}
?>