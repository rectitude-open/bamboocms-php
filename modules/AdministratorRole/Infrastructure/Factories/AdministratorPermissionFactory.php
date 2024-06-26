<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AdministratorRole\Domain\Models\AdministratorPermission;

class AdministratorPermissionFactory extends Factory
{
    protected $model = AdministratorPermission::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}
