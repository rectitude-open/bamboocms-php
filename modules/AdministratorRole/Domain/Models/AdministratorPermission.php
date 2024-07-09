<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AdministratorRole\Infrastructure\Factories\AdministratorPermissionFactory;
use Modules\AdministratorRole\Infrastructure\Scopes\AdministratorPermissionSearchScope;

class AdministratorPermission extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function role()
    {
        return $this->belongsToMany(AdministratorRole::class, 'pivot_administrator_role_permission');
    }

    public function scopeSearch(Builder $query, array $params = [])
    {
        return AdministratorPermissionSearchScope::apply($query, $params);
    }

    protected static function newFactory(): Factory
    {
        return AdministratorPermissionFactory::new();
    }
}
