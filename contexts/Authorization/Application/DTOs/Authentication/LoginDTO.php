<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\Authentication;

class LoginDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly bool $remember,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['email'],
            $data['password'],
            $data['remember'] ?? false,
        );
    }
}
