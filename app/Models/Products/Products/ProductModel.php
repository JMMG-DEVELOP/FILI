<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;
use App\Services\StockService;
class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'code',
        'description',
        'section',
        'brand',
        'sales',
        'status',
        'iva',

    ];

    public function add(array $data)
    {
        if ($this->insert($data) === false) {
            return false;
        }

        return $this->getInsertID();
    }
    public function getByCode(string $productCode)
    {
        $db = $this->db;

        // 1️⃣ Producto + sección + marca + IVA + costos + precios
        $product = $db->table('products p')
            ->select([
                'p.id',
                'p.code',
                'p.description',
                'p.status',

                // SECCIÓN
                'p.section AS section_id',
                's.name AS section',

                // MARCA
                'p.brand AS brand_id',
                'b.name AS brand',

                // SALES
                'sl.id AS sales_id',
                'sl.name AS sales',

                // IVA
                'p.iva AS iva_id',
                'i.name AS iva',

                // COSTOS
                'COALESCE(pc.cost, 0) AS cost',


                // PRECIOS
                'COALESCE(pr.price_one, 0) AS price_one',
                'COALESCE(pr.price_two, 0) AS price_two',
                'COALESCE(pr.price_card, 0) AS price_card',
                'COALESCE(pr.price_three, 0) AS price_three',

                // CANTIDADES
                'COALESCE(pr.cant_two, 0) AS cant_two',
                'COALESCE(pr.cant_three, 0) AS cant_three',
            ])
            ->join('products_section s', 's.id = p.section', 'left')
            ->join('products_brands b', 'b.id = p.brand', 'left')
            ->join('products_sales sl', 'sl.id = p.sales', 'left')
            ->join('products_iva i', 'i.id = p.iva', 'left')
            ->join('products_prices pr', 'pr.product = p.code', 'left')
            ->join('products_cost pc', 'pc.product = p.code', 'left')
            ->where('p.code', $productCode)
            ->get()
            ->getRowArray();

        if (!$product) {
            return false;
        }

        // 2️⃣ Obtener TODAS las sucursales
        $branches = $db->table('sucursals')
            ->select('id')
            ->get()
            ->getResultArray();

        // Inicializar stock en 0
        $stock = [];
        foreach ($branches as $branch) {
            $stock[$branch['id']] = 0;
        }

        // 3️⃣ Stock REAL (blindado con GROUP BY)
        $stockRows = $db->table('products_stock')
            ->select('sucursal, SUM(stock) AS stock')
            ->where('product', $productCode)
            ->groupBy('sucursal')
            ->get()
            ->getResultArray();

        foreach ($stockRows as $row) {
            $stock[$row['sucursal']] = (float) $row['stock'];
        }

        // 4. Reestructurar precios y cantidades
        $product['prices'] = [
            'price_one' => (float) $product['price_one'],
            'price_two' => (float) $product['price_two'],
            'price_card' => (float) $product['price_card'],
            'price_three' => (float) $product['price_three'],
            'cant_two' => (float) $product['cant_two'],
            'cant_three' => (float) $product['cant_three'],
        ];

        unset(
            $product['price_one'],
            $product['price_two'],
            $product['price_card'],
            $product['price_three'],
            $product['cant_two'],
            $product['cant_three']
        );

        // 5. Agregar stock completo
        $product['stock'] = $stock;

        return $product;
    }

    public function getBySearch(string $search)
    {
        $db = $this->db;

        $search = trim($search);

        if ($search === '') {
            return [];
        }

        /**
         * 1️⃣ PRODUCTOS (limit 4)
         */
        $products = $db->table('products p')
            ->select([
                'p.code',
                'p.description',
                'COALESCE(pr.price_one, 0) AS price_one'
            ])
            ->join('products_prices pr', 'pr.product = p.code', 'left')
            ->groupStart()
            ->like('p.code', $search)
            ->orLike('p.description', $search)
            ->groupEnd()
            ->orderBy(
                "CASE 
                WHEN p.code = " . $db->escape($search) . " THEN 0
                ELSE 1
            END",
                'ASC',
                false
            )
            ->orderBy('p.description', 'ASC')
            ->limit(4)
            ->get()
            ->getResultArray();

        if (!$products) {
            return [];
        }

        /**
         * 2️⃣ OBTENER CÓDIGOS
         */
        $codes = array_column($products, 'code');

        /**
         * 3️⃣ TODAS LAS SUCURSALES
         */
        $branches = $db->table('sucursals')
            ->select('id')
            ->get()
            ->getResultArray();

        $stockBase = [];
        foreach ($branches as $b) {
            $stockBase[$b['id']] = 0;
        }

        /**
         * 4️⃣ STOCK REAL POR SUCURSAL
         */
        $stockRows = $db->table('products_stock')
            ->select('product, sucursal, SUM(stock) AS stock')
            ->whereIn('product', $codes)
            ->groupBy(['product', 'sucursal'])
            ->get()
            ->getResultArray();

        /**
         * 5️⃣ REESTRUCTURAR
         */
        $stockMap = [];

        foreach ($stockRows as $row) {
            $stockMap[$row['product']][$row['sucursal']] = (float) $row['stock'];
        }

        /**
         * 6️⃣ ASIGNAR STOCK A CADA PRODUCTO
         */
        foreach ($products as &$product) {

            $stock = $stockBase; // base en 0

            if (isset($stockMap[$product['code']])) {
                foreach ($stockMap[$product['code']] as $sucursal => $qty) {
                    $stock[$sucursal] = $qty;
                }
            }

            $product['stock'] = $stock;
        }

        return $products;
    }


    public function updateInlineField(
        int $productId,
        string $field,
        $value
    ): array {

        $db = $this->db;

        // ============================================================
        // 1. VALIDAR CAMPOS PERMITIDOS
        // ============================================================

        $allowedFields = [
            'description',
            'cost',
            'price_1',
            'price_2',
            'price_three',
            'stock_s1',
            'stock_s2'
        ];

        if (!in_array($field, $allowedFields, true)) {
            return [
                'status' => false,
                'error' => 'Campo no permitido.'
            ];
        }

        // ============================================================
        // 2. BUSCAR PRODUCTO
        // ============================================================

        $product = $db->table('products')
            ->select('id, code, description')
            ->where('id', $productId)
            ->get()
            ->getRowArray();

        if (!$product) {
            return [
                'status' => false,
                'error' => 'El producto no existe.'
            ];
        }

        $productCode = $product['code'];

        // ============================================================
        // 3. VALIDAR Y PREPARAR VALOR
        // ============================================================

        if ($field === 'description') {

            $value = trim((string) $value);

            if ($value === '') {
                return [
                    'status' => false,
                    'error' => 'La descripción no puede estar vacía.'
                ];
            }

            if (mb_strlen($value) > 200) {
                return [
                    'status' => false,
                    'error' => 'La descripción no puede superar los 200 caracteres.'
                ];
            }

        } elseif (
            in_array($field, [
                'cost',
                'price_1',
                'price_2',
                'price_three'
            ], true)
        ) {

            $value = preg_replace('/[^\d-]/', '', (string) $value);

            if ($value === '' || !is_numeric($value)) {
                return [
                    'status' => false,
                    'error' => 'El importe ingresado no es válido.'
                ];
            }

            $value = (int) $value;

            if ($value < 0) {
                return [
                    'status' => false,
                    'error' => 'El importe no puede ser negativo.'
                ];
            }

        } else {

            $value = str_replace(',', '.', trim((string) $value));

            if ($value === '' || !is_numeric($value)) {
                return [
                    'status' => false,
                    'error' => 'La cantidad ingresada no es válida.'
                ];
            }

            $value = (float) $value;

            if ($value < 0) {
                return [
                    'status' => false,
                    'error' => 'El stock no puede ser negativo.'
                ];
            }
        }

        // ============================================================
        // 4. INICIAR TRANSACCIÓN
        // ============================================================

        $db->transBegin();

        try {

            // ========================================================
            // DESCRIPCIÓN
            // ========================================================

            if ($field === 'description') {

                $db->table('products')
                    ->where('id', $productId)
                    ->update([
                        'description' => $value
                    ]);

            }

            // ========================================================
            // COSTO
            // ========================================================
            elseif ($field === 'cost') {

                $costExists = $db->table('products_cost')
                    ->where('product', $productCode)
                    ->countAllResults();

                if ($costExists > 0) {

                    $db->table('products_cost')
                        ->where('product', $productCode)
                        ->update([
                            'cost' => $value
                        ]);

                } else {

                    $db->table('products_cost')
                        ->insert([
                            'product' => $productCode,
                            'cost' => $value
                        ]);
                }

            }

            // ========================================================
            // PRECIOS
            // ========================================================
            elseif (
                in_array($field, [
                    'price_1',
                    'price_2',
                    'price_three'
                ], true)
            ) {

                $priceColumns = [
                    'price_1' => 'price_one',
                    'price_2' => 'price_two',
                    'price_three' => 'price_three'
                ];

                $column = $priceColumns[$field];

                $priceExists = $db->table('products_prices')
                    ->where('product', $productCode)
                    ->countAllResults();

                if ($priceExists > 0) {

                    $db->table('products_prices')
                        ->where('product', $productCode)
                        ->update([
                            $column => $value
                        ]);

                } else {

                    $db->table('products_prices')
                        ->insert([
                            'product' => $productCode,
                            $column => $value
                        ]);
                }

            }

            // ========================================================
            // STOCK POR SUCURSAL + HISTORIAL
            // ========================================================
            elseif (
                in_array($field, [
                    'stock_s1',
                    'stock_s2'
                ], true)
            ) {

                $branches = [
                    'stock_s1' => 1,
                    'stock_s2' => 2
                ];

                $sucursal = $branches[$field];

                // ----------------------------------------------------
                // OBTENER STOCK ACTUAL BLOQUEANDO LOS REGISTROS
                // ----------------------------------------------------

                $stockRows = $db->query(
                    "SELECT id, stock
                 FROM products_stock
                 WHERE product = ? AND sucursal = ?
                 ORDER BY id ASC
                 FOR UPDATE",
                    [$productCode, $sucursal]
                )->getResultArray();

                $currentStock = 0;

                foreach ($stockRows as $stockRow) {
                    $currentStock += (float) $stockRow['stock'];
                }

                // ----------------------------------------------------
                // CALCULAR DIFERENCIA
                // ----------------------------------------------------

                $adjustment = round($value - $currentStock, 3);

                // ----------------------------------------------------
                // ACTUALIZAR STOCK SOLAMENTE SI HAY DIFERENCIA
                // ----------------------------------------------------

                if ($adjustment != 0.0) {

                    if (!empty($stockRows)) {

                        // Ajustar solamente la primera fila.
                        // Las demás filas permanecen intactas.

                        $firstStock = (float) $stockRows[0]['stock'];

                        $updated = $db->table('products_stock')
                            ->where('id', $stockRows[0]['id'])
                            ->update([
                                'stock' => round(
                                    $firstStock + $adjustment,
                                    3
                                )
                            ]);

                        if (!$updated) {
                            throw new \RuntimeException(
                                'No se pudo actualizar el stock.'
                            );
                        }

                    } else {

                        // Crear registro si todavía no existe.

                        $inserted = $db->table('products_stock')
                            ->insert([
                                'product' => $productCode,
                                'sucursal' => $sucursal,
                                'stock' => $value
                            ]);

                        if (!$inserted) {
                            throw new \RuntimeException(
                                'No se pudo crear el registro de stock.'
                            );
                        }
                    }

                    // // ------------------------------------------------
// REGISTRAR MOVIMIENTO TIPO 6
// ------------------------------------------------

                    $userId = (int) session('id');

                    if ($userId <= 0) {
                        throw new \RuntimeException(
                            'No se pudo identificar al usuario que modifica el stock.'
                        );
                    }

                    $stockService = new StockService();

                    $movementId = $stockService->history_movements_register([
                        'product' => $productCode,
                        'movement' => 6,
                        'quantity' => $value, // STOCK FINAL
                        'user' => $userId,
                        'sucursal' => $sucursal
                    ], $db);

                    if (!$movementId) {
                        throw new \RuntimeException(
                            'No se pudo registrar el historial del movimiento.'
                        );
                    }
                }
            }

            // ========================================================
            // 5. VERIFICAR TRANSACCIÓN
            // ========================================================

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'No se pudo completar la transacción.'
                );
            }

            $db->transCommit();

            return [
                'status' => true,
                'value' => $value,
                'field' => $field
            ];

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'Error updateInlineField: ' .
                $e->getMessage() .
                ' | Archivo: ' . $e->getFile() .
                ' | Línea: ' . $e->getLine()
            );

            return [
                'status' => false,
                'error' => 'No se pudo guardar el cambio.'
            ];
        }
    }

    public function adjustStock(
        string $productCode,
        int $sucursalId,
        float $adjustment,
        int $userId,
        int $movementType = 3
    ): array {

        $db = $this->db;

        // =====================================================
        // 1. INICIAR TRANSACCIÓN
        // =====================================================

        $db->transBegin();

        try {

            // =====================================================
            // 2. BUSCAR STOCK ACTUAL Y BLOQUEAR REGISTROS
            // =====================================================

            $builder = $db->table('products_stock')
                ->select('id, stock')
                ->where('product', $productCode)
                ->where('sucursal', $sucursalId)
                ->orderBy('id', 'ASC');

            $sql = $builder->getCompiledSelect();

            $rows = $db->query($sql . ' FOR UPDATE')->getResultArray();

            // =====================================================
            // 3. CALCULAR STOCK ACTUAL
            // =====================================================

            $currentStock = 0;

            foreach ($rows as $row) {
                $currentStock += (float) $row['stock'];
            }

            // =====================================================
            // 4. CALCULAR NUEVO STOCK
            // =====================================================

            $newStock = $currentStock + $adjustment;

            if ($newStock < 0) {

                $db->transRollback();

                return [
                    'success' => false,
                    'message' => 'STOCK INSUFICIENTE',
                    'stock' => $currentStock
                ];
            }

            // =====================================================
            // 5. ACTUALIZAR O INSERTAR STOCK
            // =====================================================

            if (!empty($rows)) {

                $updated = $db->table('products_stock')
                    ->where('id', $rows[0]['id'])
                    ->update([
                        'stock' => (float) $rows[0]['stock'] + $adjustment
                    ]);

                if (!$updated) {
                    throw new \RuntimeException(
                        'No se pudo actualizar el stock.'
                    );
                }

            } else {

                if ($adjustment > 0) {

                    $inserted = $db->table('products_stock')
                        ->insert([
                            'product' => $productCode,
                            'sucursal' => $sucursalId,
                            'stock' => $adjustment
                        ]);

                    if (!$inserted) {
                        throw new \RuntimeException(
                            'No se pudo insertar el stock.'
                        );
                    }
                }
            }

            // =====================================================
            // 6. REGISTRAR MOVIMIENTO DE STOCK
            // =====================================================

            $stockService = new StockService();

            $movementId = $stockService->history_movements_register([
                'product' => $productCode,
                'movement' => $movementType,
                'quantity' => $adjustment,
                'user' => $userId,
                'sucursal' => $sucursalId
            ], $db);

            if ($movementId === false) {
                throw new \RuntimeException(
                    'No se pudo registrar el historial de stock.'
                );
            }

            // =====================================================
            // 7. VERIFICAR TRANSACCIÓN
            // =====================================================

            if ($db->transStatus() === false) {
                throw new \RuntimeException(
                    'Error en la transacción de stock.'
                );
            }

            // =====================================================
            // 8. CONFIRMAR TRANSACCIÓN
            // =====================================================

            $db->transCommit();

            return [
                'success' => true,
                'message' => 'STOCK ACTUALIZADO',
                'stock' => $newStock,
                'movement_id' => $movementId
            ];

        } catch (\Throwable $e) {

            $db->transRollback();

            log_message(
                'error',
                'ERROR AJUSTANDO STOCK: ' . $e->getMessage()
            );

            log_message(
                'error',
                'ARCHIVO: ' . $e->getFile() .
                ' | LINEA: ' . $e->getLine()
            );

            return [
                'success' => false,
                'message' => 'ERROR AL ACTUALIZAR STOCK'
            ];
        }
    }
    public function getLastPurchaseHistory($code)
    {
        return $this->db->table('products_cost pc')
            ->select([
                'csh.id',
                'csh.price',
                'csh.date',
                'csh.quantity',
                'csh.cost',
                'csh.provider',
                'p.name AS provider_name',
                'u.name AS user_name'
            ])
            ->join(
                'cost_shop_history csh',
                'csh.cost = pc.id',
                'inner'
            )
            ->join(
                'provider p',
                'p.id = csh.provider',
                'left'
            )
            ->join(
                'users u',
                'u.id = csh.user',
                'left'
            )
            ->where('pc.product', $code)
            ->orderBy('csh.date', 'DESC')
            ->orderBy('csh.id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

}
