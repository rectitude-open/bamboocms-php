<?php

declare(strict_types=1);

namespace Contexts\Shared\Infrastructure\Traits;

use Contexts\Shared\Infrastructure\Scopes\SortingScope;
use Illuminate\Database\Eloquent\Builder;

trait HasSortingScopeTrait
{
    public function scopeSorting(Builder $query, array $params = [])
    {
        return SortingScope::apply($query, $params);
    }
}
