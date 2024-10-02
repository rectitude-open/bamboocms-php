<?php

declare(strict_types=1);

namespace App\Http\Scopes;

use App\Exceptions\SysException;
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
                throw new SysException('Undefined filter rule: '.$field);
            }
        }
    }

    abstract protected static function getFiltersConfig(): array;
}
