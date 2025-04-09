<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Factories;

use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = ArticleRecord::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'status' => $this->faker->randomElement([0, 1, 2, 3]),
            'author_id' => $this->faker->randomNumber(),
            'created_at' => $this->faker->dateTime,
        ];
    }
}
