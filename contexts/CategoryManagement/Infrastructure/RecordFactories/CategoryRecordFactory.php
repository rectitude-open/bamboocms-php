<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure\RecordFactories;

use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryRecordFactory extends Factory
{
    protected $model = CategoryRecord::class;

    public function definition(): array
    {
        return [
            'label' => $this->faker->word,
            'status' => $this->faker->randomElement([0, 1, 2]),
            'created_at' => $this->faker->dateTime,
        ];
    }
}
