<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

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
        'price',
        'cost',
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
                'COALESCE(pc.other_cost, 0) AS other_cost',

                // PRECIOS
                'COALESCE(pr.price_one, 0) AS price_one',
                'COALESCE(pr.price_two, 0) AS price_two',
                'COALESCE(pr.price_card, 0) AS price_card',

                // CANTIDADES
                // 'COALESCE(pr.cant_one, 0) AS cant_one',
                'COALESCE(pr.cant_two, 0) AS cant_two',
                // 'COALESCE(pr.cant_three, 0) AS cant_three'
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

        // 4️⃣ Reestructurar precios
        $product['prices'] = [
            'price_one' => (int) $product['price_one'],
            'price_two' => (int) $product['price_two'],
            'price_card' => (int) $product['price_card'],
        ];

        unset(
            $product['price_one'],
            $product['price_two'],
            $product['price_card']
        );

        // 5️⃣ Agregar stock completo y limpio
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



    // {
    //     $db = $this->db;

    //     // 1️⃣ Producto + sección + marca + IVA + precios + costos
    //     $product = $db->table('products p')
    //         ->select([
    //             'p.id',
    //             'p.code',
    //             'p.description',
    //             'p.status',

    //             // SECCIÓN
    //             'p.section AS section_id',
    //             's.name AS section',

    //             // MARCA
    //             'p.brand AS brand_id',
    //             'b.name AS brand',

    //             // IVA
    //             'p.iva AS iva_id',
    //             'i.name AS iva',

    //             // COSTOS
    //             'COALESCE(pc.cost, 0) AS cost',
    //             'COALESCE(pc.other_cost, 0) AS other_cost',

    //             // PRECIOS
    //             'COALESCE(pr.price_one, 0) AS price_one',
    //             'COALESCE(pr.price_two, 0) AS price_two',
    //             'COALESCE(pr.price_three, 0) AS price_three'
    //         ])
    //         ->join('products_section s', 's.id = p.section', 'left')
    //         ->join('products_brands b', 'b.id = p.brand', 'left')
    //         ->join('products_iva i', 'i.id = p.iva', 'left')
    //         ->join('products_prices pr', 'pr.product = p.code', 'left')
    //         ->join('products_cost pc', 'pc.product = p.code', 'left')
    //         ->where('p.code', $productCode)
    //         ->get()
    //         ->getRowArray();

    //     if (!$product) {
    //         return false;
    //     }

    //     // 2️⃣ Stock por sucursal
    //     $stockRows = $db->table('products_stock')
    //         ->select(['sucursal', 'stock'])
    //         ->where('product', $productCode)
    //         ->get()
    //         ->getResultArray();

    //     $stock = [];
    //     foreach ($stockRows as $row) {
    //         $stock[$row['sucursal']] = (float) $row['stock'];
    //     }

    //     // 3️⃣ Reestructurar precios
    //     $product['prices'] = [
    //         'price_one' => (int) $product['price_one'],
    //         'price_two' => (int) $product['price_two'],
    //         'price_three' => (int) $product['price_three'],
    //     ];

    //     unset(
    //         $product['price_one'],
    //         $product['price_two'],
    //         $product['price_three']
    //     );

    //     // 4️⃣ Agregar stock
    //     $product['stock'] = $stock;

    //     return $product;
    // }


    // public function getByCode(string $productCode)
    // {
    //     $db = $this->db;

    //     // 1️⃣ Producto + sección + marca + precios + IVA + costos
    //     $product = $db->table('products p')
    //         ->select([
    //             'p.id',
    //             'p.code',
    //             'p.description',
    //             'p.status',

    //             's.name AS section',
    //             'b.name AS brand',

    //             'p.iva',

    //             'COALESCE(pc.cost, 0) AS cost',
    //             'COALESCE(pc.other_cost, 0) AS other_cost',

    //             'COALESCE(pr.price_one, 0) AS price_one',
    //             'COALESCE(pr.price_two, 0) AS price_two',
    //             'COALESCE(pr.price_three, 0) AS price_three'
    //         ])
    //         ->join('products_section s', 's.id = p.section', 'left')
    //         ->join('products_brands b', 'b.id = p.brand', 'left')
    //         ->join('products_prices pr', 'pr.product = p.code', 'left')
    //         ->join('products_cost pc', 'pc.product = p.code', 'left')
    //         ->where('p.code', $productCode)
    //         ->get()
    //         ->getRowArray();

    //     if (!$product) {
    //         return false; // Producto no existe
    //     }

    //     // 2️⃣ Stock por sucursal (SEGÚN DIAGRAMA)
    //     $stockRows = $db->table('products_stock ps')
    //         ->select([
    //             'ps.sucursal',
    //             'ps.stock'
    //         ])
    //         ->where('ps.product', $productCode)
    //         ->get()
    //         ->getResultArray();

    //     $stock = [];
    //     foreach ($stockRows as $row) {
    //         $stock[$row['sucursal']] = (float) $row['stock'];
    //     }

    //     // 3️⃣ Reestructurar precios
    //     $product['prices'] = [
    //         'price_one' => (int) $product['price_one'],
    //         'price_two' => (int) $product['price_two'],
    //         'price_three' => (int) $product['price_three'],
    //     ];

    //     unset(
    //         $product['price_one'],
    //         $product['price_two'],
    //         $product['price_three']
    //     );

    //     // 4️⃣ Agregar stock
    //     $product['stock'] = $stock;

    //     return $product;
    // }

}