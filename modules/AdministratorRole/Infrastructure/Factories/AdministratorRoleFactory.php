<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Infrastructure\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

class AdministratorRoleFactory extends Factory
{
    protected $model = AdministratorRole::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
        ];
    }
}
