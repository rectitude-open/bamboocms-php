<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Scopes;

use App\Http\Scopes\BaseSearchScope;
use Illuminate\Contracts\Database\Eloquent\Builder;

class AdministratorPermissionSearchScope extends BaseSearchScope
{
    protected static function getFiltersConfig(): array
    {
        return [
            'id' => function (Builder $query, $value) {
                $query->when((int) $value > 0, function ($query) use ($value) {
                    $query->where('id', $value);
                });
            },
            'name' => function (Builder $query, $value) {
                $query->when(! empty($value), function ($query) use ($value) {
                    $query->where('name', $value);
                });
            },
        ];
    }
}
