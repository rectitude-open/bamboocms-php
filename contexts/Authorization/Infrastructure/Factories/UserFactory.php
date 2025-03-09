<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure\Factories;

use Contexts\Authorization\Infrastructure\Records\UserRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = UserRecord::class;

    public function definition(): array
    {
        return [
            'display_name' => $this->faker->word,
            'status' => $this->faker->randomElement([0, 1, 2]),
            'email' => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password,
            'created_at' => $this->faker->dateTime,
        ];
    }
}
