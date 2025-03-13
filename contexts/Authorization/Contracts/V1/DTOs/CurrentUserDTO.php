<?php

declare(strict_types=1);

namespace Contexts\Authorization\Contracts\V1\DTOs;

class CurrentUserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $displayName,
        public readonly string $email,
        /** @var RoleDTO[] */
        public readonly array $roles,
    ) {}
}
