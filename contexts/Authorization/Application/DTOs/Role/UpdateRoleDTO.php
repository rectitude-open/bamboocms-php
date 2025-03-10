<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\DTOs\Role;

class UpdateRoleDTO
{
    public function __construct(
        public readonly ?string $label,
        public readonly ?string $status,
        public readonly ?string $created_at
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['label'] ?? null,
            $data['status'] ?? null,
            $data['created_at'] ?? null,
        );
    }
}
