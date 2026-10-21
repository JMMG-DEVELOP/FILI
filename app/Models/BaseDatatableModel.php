<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;

abstract class BaseDatatableModel extends Model
{
    protected $table;

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    /**
     * Tabla utilizada para contar registros.
     * Si no se configura, utiliza $table.
     *
     * Ejemplo:
     * protected $table = 'products p';
     * protected $countTable = 'products p';
     */
    protected ?string $countTable = null;

    /**
     * Columna única de la tabla principal para contar registros.
     *
     * Ejemplo:
     * protected string $countPrimaryKey = 'p.id';
     */
    protected string $countPrimaryKey = 'id';

    /**
     * Configuración de consultas.
     */
    protected array $select = [];

    protected array $joins = [];

    /**
     * Indica si el conteo filtrado necesita JOIN.
     *
     * Activarlo cuando columnSearchCount incluya columnas
     * de otras tablas.
     */
    protected bool $joinsForCount = false;

    /**
     * Columnas permitidas para ordenar.
     * Las claves corresponden al índice de columna de DataTables.
     */
    protected array $columnOrder = [];

    /**
     * Columnas que se pueden buscar en getRows().
     */
    protected array $columnSearch = [];

    /**
     * Columnas utilizadas para contar los resultados filtrados.
     */
    protected array $columnSearchCount = [];

    /**
     * Orden predeterminado.
     */
    protected array $defaultOrder = [
        'id' => 'DESC'
    ];

    /**
     * Límites de paginación.
     */
    protected int $defaultPageLength = 10;

    protected int $maxPageLength = 100;

    /**
     * Construye la consulta base.
     *
     * $forCount = true:
     * Utiliza la tabla configurada para los conteos.
     *
     * $forCount = false:
     * Utiliza la tabla principal, SELECT y JOIN.
     */
    protected function baseQuery(
        bool $forCount = false,
        ?bool $includeJoins = null
    ): BaseBuilder {
        $table = $forCount
            ? ($this->countTable ?? $this->table)
            : $this->table;

        $builder = $this->db->table($table);

        if (!$forCount && !empty($this->select)) {
            $builder->select($this->select);
        }

        $useJoins = $includeJoins
            ?? (!$forCount);

        if ($useJoins) {
            foreach ($this->joins as $join) {
                [$joinTable, $condition, $type] = $join;

                $builder->join(
                    $joinTable,
                    $condition,
                    $type ?? 'left'
                );
            }
        }

        return $builder;
    }

    /**
     * Obtiene el texto de búsqueda enviado por DataTables.
     */
    protected function getSearchValue(array $post): string
    {
        $search = $post['search']['value'] ?? '';

        if (!is_scalar($search)) {
            return '';
        }

        return trim((string) $search);
    }

    /**
     * Aplica la búsqueda global.
     */
    protected function applySearch(
        BaseBuilder $builder,
        array $post,
        bool $forCount = false
    ): void {
        $search = $this->getSearchValue($post);

        $columns = $forCount && !empty($this->columnSearchCount)
            ? $this->columnSearchCount
            : $this->columnSearch;

        if ($search === '' || empty($columns)) {
            return;
        }

        $builder->groupStart();

        foreach ($columns as $column) {
            $builder->orLike($column, $search);
        }

        $builder->groupEnd();
    }

    /**
     * Aplica el orden solicitado por DataTables.
     */
    protected function applyOrder(
        BaseBuilder $builder,
        array $post
    ): void {
        $order = $post['order'][0] ?? null;

        if (is_array($order)) {
            $index = (int) ($order['column'] ?? -1);

            $column = $this->columnOrder[$index] ?? null;

            $direction = strtolower(
                (string) ($order['dir'] ?? 'asc')
            );

            if (
                $column !== null &&
                in_array($direction, ['asc', 'desc'], true)
            ) {
                $builder->orderBy(
                    $column,
                    strtoupper($direction)
                );

                return;
            }
        }

        foreach ($this->defaultOrder as $column => $direction) {
            $direction = strtoupper((string) $direction);

            if (!in_array($direction, ['ASC', 'DESC'], true)) {
                $direction = 'DESC';
            }

            $builder->orderBy($column, $direction);
        }
    }

    /**
     * Obtiene los registros de la página actual.
     */
    public function getRows(array $post): array
    {
        $builder = $this->baseQuery();

        $this->applySearch($builder, $post);

        $this->applyOrder($builder, $post);

        $start = max(
            0,
            (int) ($post['start'] ?? 0)
        );

        $length = (int) (
            $post['length'] ?? $this->defaultPageLength
        );

        if ($length < 1) {
            $length = $this->defaultPageLength;
        }

        $length = min($length, $this->maxPageLength);

        $builder->limit($length, $start);

        return $builder
            ->get()
            ->getResultArray();
    }

    /**
     * Cuenta todos los registros sin aplicar búsqueda.
     */
    public function countAll(): int
    {
        return $this->countDistinctRows(
            $this->baseQuery(true)
        );
    }

    /**
     * Cuenta los registros que coinciden con la búsqueda.
     */
    public function countFiltered(array $post): int
    {
        $builder = $this->baseQuery(
            true,
            $this->joinsForCount
        );

        $this->applySearch(
            $builder,
            $post,
            true
        );

        return $this->countDistinctRows($builder);
    }

    /**
     * Cuenta registros únicos por la clave principal.
     *
     * Evita contar dos veces el mismo registro cuando
     * una consulta con JOIN devuelve varias coincidencias.
     */
    protected function countDistinctRows(
        BaseBuilder $builder
    ): int {
        $primaryKey = $this->db->protectIdentifiers(
            $this->countPrimaryKey
        );

        $builder->select(
            "COUNT(DISTINCT {$primaryKey}) AS total",
            false
        );

        $row = $builder->get()->getRowArray();

        return (int) ($row['total'] ?? 0);
    }
}
