<?php

declare(strict_types=1);

namespace Contexts\Authorization\Contracts\V1\DTOs;

class RoleDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $label
    ) {}
}
