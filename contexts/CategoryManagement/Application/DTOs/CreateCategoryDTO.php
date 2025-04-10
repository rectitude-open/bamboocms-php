<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\DTOs;

class CreateCategoryDTO
{
    public function __construct(
        public readonly string $label,
        public readonly ?string $createdAt
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['label'],
            $data['created_at'] ?? null,
        );
    }
}
