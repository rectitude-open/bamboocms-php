<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

use Contexts\Authorization\Infrastructure\Repositories\RoleRepository;

class RoleResolver
{
    public function __construct(
        private readonly RoleRepository $repository
    ) {}

    public function resolveIds(array $roleLabels)
    {
        return $this->repository->getByLabels($roleLabels)
            ->map(fn ($role) => $role->getId())
            ->toArray();
    }
}
