<?php

declare(strict_types=1);

namespace Contexts\Shared\Infrastructure\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

class SortingScope
{
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
