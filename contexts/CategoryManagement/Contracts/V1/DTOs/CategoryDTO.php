<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Contracts\V1\DTOs;

enum CategoryStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case DELETED = 'deleted';
}

class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $label,
        public readonly CategoryStatus $status,
    ) {}
}
