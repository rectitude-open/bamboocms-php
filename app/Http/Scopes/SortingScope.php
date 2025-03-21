<?php

declare(strict_types=1);

namespace App\Http\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

class SortingScope
{
    /**
     * @param array{
     *   sorting: array<int, array{
     *      field: string,
     *      direction?: string
     *   }>
     * } $params
     * @return void
     */
    public static function apply(Builder $query, array $sorting = [])
    {
        if (empty($sorting)) {
            $sorting = [['field' => 'id', 'direction' => 'desc']];
        }

        foreach ($sorting as $sort) {
            $column = $sort['field'] ?? null;
            if ($column === null) {
                continue;
            }
            $direction = ($sort['direction'] ?? 'asc') == 'asc' ? 'asc' : 'desc';
            $query->orderBy($column, $direction);
        }
    }
}
