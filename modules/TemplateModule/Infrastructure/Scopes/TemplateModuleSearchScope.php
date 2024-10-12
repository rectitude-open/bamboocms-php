<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Infrastructure\Scopes;

use App\Http\Scopes\BaseSearchScope;
use Illuminate\Contracts\Database\Eloquent\Builder;

class TemplateModuleSearchScope extends BaseSearchScope
{
    protected static function getFiltersConfig(): array
    {
        return [
            'id' => function (Builder $query, $value) {
                $query->when((int) $value > 0, function ($query) use ($value) {
                    $query->where('id', $value);
                });
            },
            'string' => function (Builder $query, $value) {
                $query->when(! empty($value), function ($query) use ($value) {
                    $query->where('string', 'like', '%'.$value.'%');
                });
            },
            'integer' => function (Builder $query, $value) {
                $query->when(! empty($value), function ($query) use ($value) {
                    $query->where('integer', $value);
                });
            },
            'created_at' => function (Builder $query, $value) {
                $query->when(! empty($value), function ($query) use ($value) {
                    if (! $value[0] || ! $value[1]) {
                        return;
                    }
                    $query->whereBetween('created_at', $value);
                });
            },
        ];
    }
}
