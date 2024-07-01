<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AdministratorRole\Infrastructure\Factories\AdministratorRoleFactory;
use Modules\AdministratorRole\Infrastructure\Scopes\SearchScope;

class AdministratorRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(AdministratorPermission::class, 'pivot_administrator_role_permission');
    }

    public function scopeSearch(Builder $query, array $params = [])
    {
        return SearchScope::apply($query, $params);
    }

    protected static function newFactory(): Factory
    {
        return AdministratorRoleFactory::new();
    }
}
