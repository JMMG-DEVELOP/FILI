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

    public function add($data)
    {
        $this->db->transStart();

        $inserted = $this->insert($data);

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


}