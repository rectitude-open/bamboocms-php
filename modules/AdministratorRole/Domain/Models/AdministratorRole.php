<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Domain\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AdministratorRole\Infrastructure\Factories\AdministratorRoleFactory;

class AdministratorRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(AdministratorPermission::class, 'pivot_administrator_role_permission');
    }

    protected static function newFactory(): Factory
    {
        return AdministratorRoleFactory::new();
    }
}
