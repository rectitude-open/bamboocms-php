<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\User;

class CreateUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $displayName,
        public readonly ?string $createdAt
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['email'],
            $data['password'],
            $data['display_name'],
            $data['created_at'] ?? null,
        );
    }
}
