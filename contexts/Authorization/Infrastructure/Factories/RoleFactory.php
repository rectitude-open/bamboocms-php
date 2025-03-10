<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure\Factories;

use Contexts\Authorization\Infrastructure\Records\RoleRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = RoleRecord::class;

    public function definition(): array
    {
        return [
            'label' => $this->faker->word,
            'status' => $this->faker->randomElement([0, 1, 2]),
            'created_at' => $this->faker->dateTime,
        ];
    }
}
