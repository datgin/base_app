<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait Pagination
{
    public function pagination(
        Builder $query,
        array $columns = ['*'],
        array $relations = [],
        array $filters = [],
        array $wheres = [],
        array $withCount = [],
        array $order = [],
        string $dateColumn = 'created_at',
        array $conditions = [],
        bool $all = false,
        array $searchable = ['name'],
        ?callable $closure = null
    ) {
        $conditions = $conditions ?: request()->all();

        $query->select($columns);

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        foreach ($wheres as $where) {
            $column = $where['column'] ?? null;
            $operator = $where['operator'] ?? '=';
            $value = $where['value'] ?? null;
            $method = $where['method'] ?? 'where';

            if (!$column) continue;

            match ($method) {
                'whereIn' => $query->whereIn($column, (array)$value),
                'whereNotIn' => $query->whereNotIn($column, (array)$value),
                'whereNull' => $query->whereNull($column),
                'whereNotNull' => $query->whereNotNull($column),
                'whereBetween' => $query->whereBetween($column, (array)$value),
                'whereDate' => $query->whereDate($column, $operator, $value),
                default => $query->where($column, $operator, $value),
            };
        }

        foreach ($filters as $filter) {
            $query->when(
                !empty($conditions[$filter]),
                fn($q) => $q->where($filter, $conditions[$filter])
            );
        }

        if (!empty($conditions['start_date']) && !empty($conditions['end_date'])) {
            $query->whereBetween($dateColumn, [
                $conditions['start_date'],
                $conditions['end_date'] . ' 23:59:59',
            ]);
        }

        if (!empty($conditions['searchText']) && !empty($searchable)) {
            $query->where(function ($q) use ($conditions, $searchable) {
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'like', '%' . $conditions['searchText'] . '%');
                }
            });
        }

        if ($closure && is_callable($closure)) {
            $closure($query);
        }

        // Auto sort from FE
        $orderField = $conditions['sort_field'] ?? null;
        $orderDirection = $conditions['sort_order'] ?? 'asc';
        if ($orderField) {
            $query->orderBy($orderField, $orderDirection);
        } elseif (!empty($order)) {
            $query->orderBy($order[0], $order[1]);
        } else {
            $query->latest('id');
        }

        logger('[QUERY_BUILDER]', ['sql' => $query->toRawSql(), 'bindings' => $query->getBindings()]);

        $perPage = intval($conditions['per_page'] ?? 10);
        $page = intval($conditions['page'] ?? 1);

        if ($all) {
            if (!empty($conditions['limit'])) {
                $limit = intval($conditions['limit']);
                $query->limit($limit);
            }
            return $query->get();
        } else {
            return $query->paginate($perPage, $columns, 'page', $page);
        }
    }
}
