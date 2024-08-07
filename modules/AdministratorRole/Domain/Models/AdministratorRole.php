<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Domain\Models;

use App\Http\Models\BaseModel;
use App\Http\Scopes\SortingScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AdministratorRole\Infrastructure\Factories\AdministratorRoleFactory;
use Modules\AdministratorRole\Infrastructure\Scopes\AdministratorRoleSearchScope;

class AdministratorRole extends BaseModel
{
    protected $fillable = ['name', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(AdministratorPermission::class, 'pivot_administrator_role_permission');
    }

    public function scopeSearch(Builder $query, array $params = [])
    {
        return AdministratorRoleSearchScope::apply($query, $params);
    }

    public function scopeSorting(Builder $query, array $params = [])
    {
        return SortingScope::apply($query, $params);
    }

    protected static function newFactory(): Factory
    {
        return AdministratorRoleFactory::new();
    }
}
