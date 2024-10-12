<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Domain\Models;

use App\Http\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\TemplateModule\Infrastructure\Factories\TemplateModuleFactory;
use Modules\TemplateModule\Infrastructure\Scopes\TemplateModuleSearchScope;

class TemplateModule extends BaseModel
{
    protected $fillable = ['string', 'integer'];

    public function scopeSearch(Builder $query, array $params = [])
    {
        return TemplateModuleSearchScope::apply($query, $params);
    }

    protected static function newFactory(): Factory
    {
        return TemplateModuleFactory::new();
    }
}
