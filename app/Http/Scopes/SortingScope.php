<?php

declare(strict_types=1);

namespace App\Http\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

class SortingScope
{
    /**
     * @param array{
     *   sorting: array<int, array{
     *      id: string,
     *      desc?: bool
     *   }>
     * } $params
     * @return void
     */
    public static function apply(Builder $query, array $params = [])
    {
        $sorting = $params['sorting'] ?? [];
        if (empty($sorting)) {
            $sorting = [['id' => 'id', 'desc' => true]];
        }

        foreach ($sorting as $sort) {
            $column = $sort['id'] ?? null;
            if ($column === null) {
                continue;
            }
            $direction = $sort['desc'] ?? true ? 'desc' : 'asc';
            $query->orderBy($column, $direction);
        }
    }
}
