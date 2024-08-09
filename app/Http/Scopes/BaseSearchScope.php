<?php

declare(strict_types=1);

namespace App\Http\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

abstract class BaseSearchScope
{
    public static function apply(Builder $query, array $params = [])
    {
        $filtersConfig = static::getFiltersConfig();

        foreach ($params['filters'] ?? [] as $filter) {
            $field = $filter['id'];
            $value = $filter['value'];

            if (isset($filtersConfig[$field])) {
                $filtersConfig[$field]($query, $value);
            } else {
                throw new \InvalidArgumentException(__('Invalid filter :field: ', ['field' => $field]));
            }
        }
    }

    abstract protected static function getFiltersConfig(): array;
}
