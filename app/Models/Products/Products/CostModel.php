<?php

namespace App\Models\Products\Products;

use CodeIgniter\Model;

class CostModel extends Model
{
    protected $table = 'products_cost';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'cost',
        'other_cost',
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