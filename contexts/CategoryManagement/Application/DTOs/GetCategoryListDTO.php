<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\DTOs;

class GetCategoryListDTO
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $label,
        public readonly ?string $status,
        public readonly ?array $createdAtRange,
        public readonly int $page,
        public readonly int $perPage,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['label'] ?? null,
            $data['status'] ?? null,
            $data['created_at_range'] ?? null,
            $data['page'] ?? 1,
            $data['per_page'] ?? 10,
        );
    }

    public function toCriteria(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'status' => $this->status,
            'created_at_range' => $this->createdAtRange,
        ];
    }
}
