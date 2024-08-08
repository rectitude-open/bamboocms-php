<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Scopes;

use Illuminate\Contracts\Database\Eloquent\Builder;

class AdministratorRoleSearchScope
{
    public static function apply(Builder $query, array $params = [])
    {
        foreach ($params['filters'] ?? [] as $filter) {
            $field = $filter['id'];
            $value = $filter['value'];

            switch ($field) {
                case 'id':
                    $query->when((int) $value > 0, function ($query) use ($value) {
                        $query->where('id', $value);
                    });
                    break;
                case 'name':
                    $query->when(! empty($value), function ($query) use ($value) {
                        $query->where('name', 'like', '%'.$value.'%');
                    });
                    break;
                default:
                    break;
            }
        }
    }
}
