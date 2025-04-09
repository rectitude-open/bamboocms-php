<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\User;

use Contexts\Shared\Application\BaseGetListDTO;

class GetUserListDTO extends BaseGetListDTO
{
    protected const ALLOWED_SORT_FIELDS = ['id', 'created_at'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $email,
        public readonly ?string $displayName,
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
            $merged['email'] ?? null,
            $merged['display_name'] ?? null,
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
            'email' => $this->email,
            'display_name' => $this->displayName,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }
}
