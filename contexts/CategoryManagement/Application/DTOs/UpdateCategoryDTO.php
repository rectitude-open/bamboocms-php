<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\DTOs;

class UpdateCategoryDTO
{
    public function __construct(
        public readonly ?string $label,
        public readonly ?string $status,
        public readonly ?string $createdAt
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['label'] ?? null,
            $data['status'] ?? null,
            $data['created_at'] ?? null,
        );
    }
}
