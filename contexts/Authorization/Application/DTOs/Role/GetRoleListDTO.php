<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\Role;

class GetRoleListDTO
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $label,
        public readonly ?string $status,
        public readonly ?array $createdAtRange,
        public readonly int $currentPage,
        public readonly int $perPage,
    ) {}

    public static function fromRequest(array $data): self
    {
        $merged = array_merge($data, self::convertFiltersToCriteria($data['filters'] ?? []));

        return new self(
            $merged['id'] ?? null,
            $merged['label'] ?? null,
            $merged['status'] ?? null,
            $merged['created_at_range'] ?? null,
            $merged['current_page'] ?? 1,
            $merged['per_page'] ?? 10,
        );
    }

    private static function convertFiltersToCriteria(array $filters): array
    {
        return collect($filters)->mapWithKeys(function ($filter) {
            $key = $filter['id'];
            if ($key === 'created_at_range') {
                $value = json_decode($filter['value'], true);
            } else {
                $value = $filter['value'];
            }

            return [$key => $value];
        })->toArray();
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
