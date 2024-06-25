<?php

namespace Modules\Administrator\Infrastructure\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Administrator\Domain\Models\Administrator;

class AdministratorFactory extends Factory
{
    protected $model = Administrator::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
        ];
    }
}
