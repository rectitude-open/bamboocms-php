<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs;

class GetUserListDTO
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $email,
        public readonly ?string $display_name,
        public readonly ?string $status,
        public readonly ?array $createdAtRange,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['email'] ?? null,
            $data['display_name'] ?? null,
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
            'email' => $this->email,
            'display_name' => $this->display_name,
            'status' => $this->status,
            'created_at_range' => $this->createdAtRange,
        ];
    }
}
