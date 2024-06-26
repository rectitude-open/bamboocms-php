<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Infrastructure\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\TemplateModule\Domain\Models\TemplateModule;

class TemplateModuleFactory extends Factory
{
    protected $model = TemplateModule::class;

    public function definition(): array
    {
        return [
        ];
    }
}
