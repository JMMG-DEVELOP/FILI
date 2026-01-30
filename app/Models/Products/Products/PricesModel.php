<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class PricesModel extends Model
{
    protected $table = 'products_prices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'price_one',
        'price_two',
        'price_three',
        'cant_one',
        'cant_two',
        'cant_three',
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