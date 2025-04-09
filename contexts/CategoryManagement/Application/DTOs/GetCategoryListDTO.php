<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Application\DTOs;

use Contexts\Shared\Application\BaseGetListDTO;

class GetCategoryListDTO extends BaseGetListDTO
{
    protected const ALLOWED_SORT_FIELDS = ['id', 'created_at'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $label,
        public readonly ?string $status,
        public readonly ?array $createdAt,
        public readonly int $currentPage,
        public readonly int $perPage,
        public readonly ?array $sorting
    ) {}

    public static function fromRequest(array $data): self
    {
        $merged = array_merge($data, self::convertFiltersToCriteria($data['filters'] ?? []));

        return new self(
            $merged['id'] ?? null,
            $merged['label'] ?? null,
            $merged['status'] ?? null,
            $merged['created_at'] ?? null,
            $merged['current_page'] ?? 1,
            $merged['per_page'] ?? 10,
            self::normalizeAndFilterSorting($merged)
        );
    }

    public function toSorting(): array
    {
        return $this->sorting;
    }

    public function toCriteria(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }
}
