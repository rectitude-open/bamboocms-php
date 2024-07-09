<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

class AdministratorPermissionSearchScope
{
    public static function apply(Builder $query, array $params = [])
    {
        $query->when(isset($params['id']), function ($query) use ($params) {
            $query->where('id', $params['id']);
        });

        $query->when(isset($params['name']), function ($query) use ($params) {
            $query->where('name', $params['name']);
        });
    }
}
