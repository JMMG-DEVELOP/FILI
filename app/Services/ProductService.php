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
        'message' => 'El código ya existe'
      ];
    }

    return [
      'status' => true,
      'message' => 'Código disponible',
    ];
  }
}
