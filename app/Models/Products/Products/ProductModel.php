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

    public function getByCode(string $productCode)
    {
        $db = $this->db;

        // 1️⃣ Producto + sección + marca + precios + IVA + costos
        $product = $db->table('products p')
            ->select([
                'p.id',
                'p.code',
                'p.description',
                'p.status',

                's.name AS section',
                'b.name AS brand',

                'p.iva',

                'COALESCE(pc.cost, 0) AS cost',
                'COALESCE(pc.other_cost, 0) AS other_cost',

                'COALESCE(pr.price_one, 0) AS price_one',
                'COALESCE(pr.price_two, 0) AS price_two',
                'COALESCE(pr.price_three, 0) AS price_three'
            ])
            ->join('products_section s', 's.id = p.section', 'left')
            ->join('products_brands b', 'b.id = p.brand', 'left')
            ->join('products_prices pr', 'pr.product = p.code', 'left')
            ->join('products_cost pc', 'pc.product = p.code', 'left')
            ->where('p.code', $productCode)
            ->get()
            ->getRowArray();

        if (!$product) {
            return false; // Producto no existe
        }

        // 2️⃣ Stock por sucursal (SEGÚN DIAGRAMA)
        $stockRows = $db->table('products_stock ps')
            ->select([
                'ps.sucursal',
                'ps.stock'
            ])
            ->where('ps.product', $productCode)
            ->get()
            ->getResultArray();

        $stock = [];
        foreach ($stockRows as $row) {
            $stock[$row['sucursal']] = (float) $row['stock'];
        }

        // 3️⃣ Reestructurar precios
        $product['prices'] = [
            'price_one' => (int) $product['price_one'],
            'price_two' => (int) $product['price_two'],
            'price_three' => (int) $product['price_three'],
        ];

        unset(
            $product['price_one'],
            $product['price_two'],
            $product['price_three']
        );

        // 4️⃣ Agregar stock
        $product['stock'] = $stock;

        return $product;
    }

}