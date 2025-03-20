<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

use App\Exceptions\BizException;
use Contexts\Authorization\Domain\Repositories\RoleRepository;

class RoleLabelUniquenessService
{
    public function __construct(
        private readonly RoleRepository $roleRepository
    ) {}

    public function ensureUnique(string $label)
    {
        if ($this->roleRepository->existsByLabel($label)) {
            throw BizException::make('Role label already exists: :label')
                ->with('label', $label);
        }
    }
}
