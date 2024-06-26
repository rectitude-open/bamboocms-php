<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Domain\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TemplateModule\Infrastructure\Factories\TemplateModuleFactory;

class TemplateModule
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [];

    protected static function newFactory(): Factory
    {
        return TemplateModuleFactory::new();
    }
}
