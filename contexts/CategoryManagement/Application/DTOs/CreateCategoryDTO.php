<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\DTOs;

class CreateCategoryDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly string $status,
        public readonly ?string $created_at
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['title'],
            $data['body'] ?? '',
            $data['status'] ?? 'draft',
            $data['created_at'] ?? null,
        );
    }
}
