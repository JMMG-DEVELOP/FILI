<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

abstract class BaseDatatableModel extends Model
{
    protected $table;

    protected array $select = [];
    protected array $joins = [];

    protected array $columnOrder = [];
    protected array $columnSearch = [];
    protected array $columnSearchCount = [];

    protected array $defaultOrder = ['id' => 'DESC'];

    /**
     * Base query
     * $forCount = true  → SIN joins ni alias
     * $forCount = false → con joins
     */
    protected function baseQuery(bool $forCount = false): BaseBuilder
    {
        if ($forCount) {
            return $this->db->table(str_replace(' p', '', $this->table));
        }

        $builder = $this->db
            ->table($this->table)
            ->select($this->select);

        foreach ($this->joins as $join) {
            [$table, $condition, $type] = $join;
            $builder->join($table, $condition, $type ?? 'left');
        }

        return $builder;
    }

    protected function applySearch(BaseBuilder $builder, array $post, bool $forCount = false): void
    {
        $search = $post['search']['value'] ?? null;
        $columns = $forCount && $this->columnSearchCount
            ? $this->columnSearchCount
            : $this->columnSearch;

        if ($search && $columns) {
            $builder->groupStart();
            foreach ($columns as $col) {
                $builder->orLike($col, $search);
            }
            $builder->groupEnd();
        }
    }

    protected function applyOrder(BaseBuilder $builder, array $post): void
    {
        if (!empty($post['order'][0])) {
            $index = (int) $post['order'][0]['column'];
            $column = $this->columnOrder[$index] ?? null;
            $dir = $post['order'][0]['dir'] ?? 'asc';

            if ($column) {
                $builder->orderBy($column, $dir);
                return;
            }
        }

        $builder->orderBy(
            key($this->defaultOrder),
            current($this->defaultOrder)
        );
    }

    public function getRows(array $post): array
    {
        $builder = $this->baseQuery(false);

        $this->applySearch($builder, $post, false);
        $this->applyOrder($builder, $post);

        if (($post['length'] ?? -1) != -1) {
            $builder->limit(
                (int) $post['length'],
                (int) ($post['start'] ?? 0)
            );
        }

        return $builder->get()->getResultArray();
    }

    public function countFiltered(array $post): int
    {
        $builder = $this->baseQuery(true);
        $this->applySearch($builder, $post, true);
        return $builder->countAllResults();
    }

    public function countAll(): int
    {
        return $this->baseQuery(true)->countAllResults();
    }
}
