<?php

namespace App\Services;

use App\Models\Products\Products\ProductModel;
use App\Models\Products\Products\StockModel;
use App\Models\Products\Products\PricesModel;
use App\Models\Products\Products\CostModel;

use CodeIgniter\Database\Exceptions\DatabaseException;

class ProductService
{
  public function save(array $data): bool
  {
    $db = db_connect();
    $db->transBegin();

    try {

      // 1️⃣ PRODUCT
      $productModel = new ProductModel();
      $productModel->insert($data['products']);

      // 2️⃣ PRICES
      $priceModel = new PricesModel();
      $priceModel->insert($data['prices']);

      // 3️⃣ COSTS
      $costModel = new CostModel();
      $costModel->insert($data['costs']);

      // 4️⃣ STOCK (batch)
      if (!empty($data['stock'])) {
        $stockModel = new StockModel();
        $stockModel->insertBatch($data['stock']);
      }

      $db->transCommit();
      return true;

    } catch (\Throwable $e) {

      $db->transRollback();
      log_message('error', $e->getMessage());
      throw new DatabaseException('Error al guardar producto');

    }
  }
  public function edit(array $data): bool
  {
    $db = db_connect();
    $db->transBegin();

    try {

      $code = $data['products']['code'];

      // 1️⃣ PRODUCT
      $productModel = new ProductModel();
      $productModel
        ->where('code', $code)
        ->set($data['products'])
        ->update();

      // 2️⃣ PRICES
      $priceModel = new PricesModel();
      $existsPrice = $priceModel->where('product', $code)->first();

      if ($existsPrice) {
        $priceModel
          ->where('product', $code)
          ->set($data['prices'])
          ->update();
      } else {
        $priceModel->insert($data['prices']);
      }

      // 3️⃣ COSTS
      $costModel = new CostModel();
      $existsCost = $costModel->where('product', $code)->first();

      if ($existsCost) {
        $costModel
          ->where('product', $code)
          ->set($data['costs'])
          ->update();
      } else {
        $costModel->insert($data['costs']);
      }

      // 4️⃣ STOCK (por sucursal)
      if (!empty($data['stock'])) {
        $stockModel = new StockModel();

        foreach ($data['stock'] as $row) {

          $existsStock = $stockModel
            ->where('product', $code)
            ->where('sucursal', $row['sucursal'])
            ->first();

          if ($existsStock) {

            $newStock = (int) $row['stock'];

            if ($newStock !== 0) {

              $currentStock = (int) $existsStock['stock'];
              $totalStock = $currentStock + $newStock;

              // 👉 evitar stock negativo
              if ($totalStock < 0) {
                $totalStock = 0;
              }

              $stockModel
                ->where('product', $code)
                ->where('sucursal', $row['sucursal'])
                ->set(['stock' => $totalStock])
                ->update();
            }

          } else {

            // 👉 si no existe registro, se inserta tal cual
            $stockModel->insert($row);
          }
        }
      }


      $db->transCommit();
      return true;

    } catch (\Throwable $e) {

      $db->transRollback();
      log_message('error', $e->getMessage());
      throw new \CodeIgniter\Database\Exceptions\DatabaseException(
        'Error al editar producto'
      );
    }
  }

  public function validate_code($code)
  {
    if (!$code) {
      return [
        'status' => false,
        'error' => 'code_empty',
        'message' => 'Código vacío'
      ];
    }

    $productModel = new ProductModel();

    if ($productModel->getByCode($code)) {
      return [
        'status' => false,
        'error' => 'code_exists',
        'message' => 'El código ya esta Registrado'
      ];
    }

    return [
      'status' => true,
      'error' => 'false',
      'message' => 'Código NO Registrado',
    ];
  }
}
