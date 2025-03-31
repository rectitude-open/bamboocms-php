<?php

declare(strict_types=1);

namespace Database\Seeders;

use Contexts\Authorization\Infrastructure\Records\RoleRecord;
use Contexts\Authorization\Infrastructure\Records\UserRecord;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUser = UserRecord::factory()->create([
            'email' => 'admin@test.com',
            'password' => password_hash('test', PASSWORD_ARGON2ID),
            'display_name' => 'Admin',
            'status' => 1,
        ]);

        $adminRole = RoleRecord::factory()->create([
            'label' => 'admin',
            'status' => 1,
        ]);

        $adminUser->roles()->attach($adminRole->id);
    }
}
