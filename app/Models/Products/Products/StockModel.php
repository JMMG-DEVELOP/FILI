<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table = 'products_stock';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'stock',
        'sucursal',
        'product'
    ];

    public function add($values)
    {
        $this->db->transStart();

        $inserted = $this->insert($values);

        if ($inserted === false) {
            $this->db->transRollback();
            return false;
        }

        $insertId = $this->getInsertID();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return false;
        }

        return $insertId;
    }

    public function discountStock($items)
    {
        $status = false;
        foreach ($items as $item) {

            $builder = $this->db->table($this->table);

            $builder->set('stock', 'stock - ' . $item['stock'], false)
                ->where('product', $item['product'])
                ->where('sucursal', $item['sucursal'])
                ->update();

            if ($this->db->affectedRows() > 0) {
                $status = true;
            }
        }

        return $status;
    }

    // public function discountStock($items)
    // {

    //     foreach ($items as $item) {

    //         $this->builder()
    //             ->set('stock', 'stock - ' . $item['stock'], false)
    //             ->where('product', $item['product'])
    //             ->where('sucursal', (int) $item['sucursal'])
    //             ->update();

    //         if ($this->db->affectedRows() > 0) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }
}