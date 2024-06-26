<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Domain\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\AdministratorRole\Infrastructure\Factories\AdministratorPermissionFactory;

class AdministratorPermission
{
    use HasFactory;

    protected $fillable = ['name'];

    protected static function newFactory(): Factory
    {
        return AdministratorPermissionFactory::new();
    }
}
