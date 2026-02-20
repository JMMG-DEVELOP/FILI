<?php

namespace App\Services;

use App\Models\Products\Products\ProductModel;
use App\Models\Products\Products\StockModel;
use App\Models\Products\Products\PricesModel;
use App\Models\Products\Products\CostModel;

use CodeIgniter\Database\Exceptions\DatabaseException;

class ProductService
{
  public function add(array $data): array
  {
    $db = db_connect();
    $db->transBegin();

    try {

      // ================= PRODUCT =================
      $productModel = new ProductModel();

      if (!$productModel->insert($data['products'])) {
        return [
          'status' => false,
          'message' => $productModel->errors()
            ? implode(', ', $productModel->errors())
            : 'Error al insertar producto'
        ];
      }

      // ================= PRICES =================
      $priceModel = new PricesModel();

      if (!$priceModel->insert($data['prices'])) {
        return [
          'status' => false,
          'message' => $priceModel->errors()
            ? implode(', ', $priceModel->errors())
            : 'Error al insertar precios'
        ];
      }

      // ================= COSTS =================
      $costModel = new CostModel();

      if (!$costModel->insert($data['costs'])) {
        return [
          'status' => false,
          'message' => $costModel->errors()
            ? implode(', ', $costModel->errors())
            : 'Error al insertar costos'
        ];
      }

      // ================= STOCK =================
      if (!empty($data['stock'])) {
        $stockModel = new StockModel();

        if (!$stockModel->insertBatch($data['stock'])) {
          return [
            'status' => false,
            'message' => 'Error al insertar stock'
          ];
        }
      }

      // ================= COMMIT =================
      if ($db->transStatus() === false) {
        $db->transRollback();

        return [
          'status' => false,
          'message' => 'Error en la transacción'
        ];
      }

      $db->transCommit();

      return [
        'status' => true,
        'message' => 'Producto registrado correctamente'
      ];

    } catch (\Throwable $e) {

      $db->transRollback();

      log_message('error', $e->getMessage());

      return [
        'status' => false,
        'message' => 'Exception: ' . $e->getMessage()
      ];
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
