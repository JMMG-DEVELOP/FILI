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

    try {

      $db->transBegin();

      // ============================================================
      // 1. PRODUCTO
      // ============================================================

      $productModel = new ProductModel();

      if (!$productModel->insert($data['products'])) {

        throw new \RuntimeException(
          implode(', ', $productModel->errors())
          ?: 'Error al insertar producto'
        );
      }

      // ============================================================
      // 2. PRECIOS
      // ============================================================

      $priceModel = new PricesModel();

      if (!$priceModel->insert($data['prices'])) {

        throw new \RuntimeException(
          implode(', ', $priceModel->errors())
          ?: 'Error al insertar precios'
        );
      }

      // ============================================================
      // 3. COSTOS
      // ============================================================

      $costModel = new CostModel();

      if (!$costModel->insert($data['costs'])) {

        throw new \RuntimeException(
          implode(', ', $costModel->errors())
          ?: 'Error al insertar costos'
        );
      }

      // ============================================================
      // 4. STOCK POR SUCURSAL
      // ============================================================

      if (!empty($data['stock'])) {

        $stockModel = new StockModel();

        if (!$stockModel->insertBatch($data['stock'])) {

          throw new \RuntimeException(
            'Error al insertar stock por sucursal'
          );
        }
      }

      // ============================================================
      // 5. VALIDAR TRANSACCIÓN
      // ============================================================

      if ($db->transStatus() === false) {

        throw new \RuntimeException(
          'Error al procesar la transacción'
        );
      }

      $db->transCommit();

      return [
        'status' => true,
        'message' => 'Producto registrado correctamente'
      ];

    } catch (\Throwable $e) {

      $db->transRollback();

      log_message(
        'error',
        'ProductService::add(): ' . $e->getMessage()
      );

      return [
        'status' => false,
        'message' => $e->getMessage()
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


      // 3. COSTOS
      if (isset($data['costs'])) {

        $costModel = new CostModel();

        $existsCost = $costModel
          ->where('product', $code)
          ->first();

        if ($existsCost) {

          $costModel
            ->where('product', $code)
            ->set($data['costs'])
            ->update();

        } else {

          $costModel->insert($data['costs']);

        }
      }



      // ==========================================
// 4. STOCK POR SUCURSAL (AJUSTE)
// ==========================================

      if (!empty($data['stock'])) {

        $stockModel = new StockModel();

        foreach ($data['stock'] as $row) {

          $branchId = (int) $row['sucursal'];
          $adjustment = (float) $row['stock'];

          // No hacer nada si el ajuste es cero
          if ($adjustment == 0) {
            continue;
          }

          // Buscar stock actual
          $existsStock = $stockModel
            ->where('product', $code)
            ->where('sucursal', $branchId)
            ->first();

          if ($existsStock) {

            $currentStock = (float) $existsStock['stock'];

            // Sumar o restar según el signo
            $newStock = $currentStock + $adjustment;

            // No permitir stock negativo
            if ($newStock < 0) {
              throw new \RuntimeException(
                "Stock insuficiente en la sucursal {$branchId}. " .
                "Stock actual: {$currentStock}. " .
                "Ajuste solicitado: {$adjustment}."
              );
            }

            $updated = $stockModel
              ->where('product', $code)
              ->where('sucursal', $branchId)
              ->set('stock', $newStock)
              ->update();

            if (!$updated) {
              throw new \RuntimeException(
                "No se pudo actualizar el stock de la sucursal {$branchId}"
              );
            }

          } else {

            // Si no existe registro, solo permitir ajuste positivo
            if ($adjustment < 0) {
              throw new \RuntimeException(
                "No existe stock registrado en la sucursal {$branchId}"
              );
            }

            $inserted = $stockModel->insert([
              'product' => $code,
              'sucursal' => $branchId,
              'stock' => $adjustment,
            ]);

            if (!$inserted) {
              throw new \RuntimeException(
                "No se pudo registrar el stock de la sucursal {$branchId}"
              );
            }
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
      'message' => 'CODIGO NO ESTA REGISTRADO',
    ];
  }
}
