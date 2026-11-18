<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class ShoppModel extends Model
{
    protected $DBGroup = 'default';

    protected $table = 'products';

    public function savePurchase(array $data): array
    {
        $db = $this->db;

        try {

            $db->transBegin();

            // ==========================================
            // 1. VERIFICAR Y BLOQUEAR PRODUCTO
            // ==========================================

            $product = $db->query(
                "SELECT code
                 FROM products
                 WHERE code = ?
                 LIMIT 1
                 FOR UPDATE",
                [$data['product']]
            )->getRowArray();

            if (!$product) {
                throw new \RuntimeException(
                    'EL PRODUCTO NO EXISTE'
                );
            }

            // ==========================================
            // 2. ACTUALIZAR COSTO
            // ==========================================

            $cost = $db->query(
                "SELECT id, cost
                 FROM products_cost
                 WHERE product = ?
                 LIMIT 1
                 FOR UPDATE",
                [$data['product']]
            )->getRowArray();

            if ($cost) {

                $db->table('products_cost')
                    ->where('id', $cost['id'])
                    ->update([
                        'cost' => $data['cost']
                    ]);

                // ID del costo existente
                $costId = (int) $cost['id'];

            } else {

                $db->table('products_cost')->insert([
                    'product' => $data['product'],
                    'cost' => $data['cost']
                ]);

                // ID del nuevo costo
                $costId = (int) $db->insertID();

            }

            // ==========================================
            // 3. ACTUALIZAR PRECIOS
            // ==========================================

            $prices = $db->table('products_prices')
                ->where('product', $data['product'])
                ->get()
                ->getRowArray();

            $priceData = [
                'price_one' => $data['price_one'],
                'price_two' => $data['price_two'],
                'price_three' => $data['price_three'],
                'cant_two' => $data['cant_two'],
                'cant_three' => $data['cant_three']
            ];

            if ($prices) {

                $db->table('products_prices')
                    ->where('id', $prices['id'])
                    ->update($priceData);

            } else {

                $priceData['product'] = $data['product'];
                $priceData['price_card'] = 0;

                $db->table('products_prices')
                    ->insert($priceData);

            }

            // ==========================================
            // 4. ACTUALIZAR STOCK
            // ==========================================

            $stock = $db->query(
                "SELECT id, stock
                 FROM products_stock
                 WHERE product = ?
                 AND sucursal = ?
                 FOR UPDATE",
                [
                    $data['product'],
                    $data['sucursal']
                ]
            )->getRowArray();

            if ($stock) {

                $db->query(
                    "UPDATE products_stock
                     SET stock = stock + ?
                     WHERE id = ?",
                    [
                        $data['quantity'],
                        $stock['id']
                    ]
                );

                $stockFinal = (float) $stock['stock']
                    + (float) $data['quantity'];

            } else {

                $stockFinal = (float) $data['quantity'];

                $db->table('products_stock')->insert([
                    'product' => $data['product'],
                    'sucursal' => $data['sucursal'],
                    'stock' => $stockFinal
                ]);

            }

            // ==========================================
            // 5. HISTORIAL DE COMPRA
            // ==========================================

            $db->table('cost_shop_history')->insert([

                // ID del registro de products_cost
                'cost' => $costId,

                // Datos de la compra
                'price' => $data['price'],
                'date' => date('Y-m-d'),
                'user' => $data['user'],
                'provider' => $data['provider'],
                'quantity' => $data['quantity']

            ]);

            // ==========================================
            // 6. VERIFICAR TRANSACCIÓN
            // ==========================================

            if ($db->transStatus() === false) {

                throw new \RuntimeException(
                    'ERROR AL GUARDAR LOS DATOS DE LA COMPRA'
                );

            }

            // ==========================================
            // 7. CONFIRMAR TRANSACCIÓN
            // ==========================================

            $db->transCommit();

            return [
                'status' => true,
                'message' => 'COMPRA REGISTRADA CORRECTAMENTE',
                'stock' => $stockFinal
            ];

        } catch (\Throwable $e) {

            // Revertir todas las operaciones
            $db->transRollback();

            log_message(
                'error',
                'ERROR SHOPP MODEL: ' . $e->getMessage()
            );

            log_message(
                'error',
                'ARCHIVO: ' . $e->getFile() .
                ' LINEA: ' . $e->getLine()
            );

            return [
                'status' => false,
                'message' => 'ERROR AL REGISTRAR LA COMPRA'
            ];

        }
    }
}
