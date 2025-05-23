<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\User;

class UpdateUserDTO
{
    public function __construct(
        public readonly ?string $email,
        public readonly ?string $displayName,
        public readonly ?string $status,
        public readonly ?string $createdAt
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['email'] ?? null,
            $data['display_name'] ?? null,
            $data['status'] ?? null,
            $data['created_at'] ?? null,
        );
    }
}
