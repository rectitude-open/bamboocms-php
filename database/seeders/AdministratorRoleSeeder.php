<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AdministratorRole\Domain\Models\AdministratorPermission;
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

class AdministratorRoleSeeder extends Seeder
{
    public function run(): void
    {
        AdministratorRole::query()->delete();
        AdministratorPermission::query()->delete();

        $role = AdministratorRole::create([
            'name' => 'administrator',
            'description' => 'Administrator',
        ]);

        $permissions = [
            'AdministratorRole',
            'Test1',
            'Test2',
        ];

        foreach ($permissions as $permission) {
            AdministratorPermission::create([
                'name' => $permission,
            ]);
        }

        $role->permissions()->attach(AdministratorPermission::all());
    }
}
