<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Policies;

use Contexts\Authorization\Domain\Services\RoleResolver;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;

class RolePolicy implements BasePolicy
{
    public function __construct(
        private RoleResolver $resolver
    ) {}

    private array $rules = [];

    public function withRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    public function evaluate(?UserIdentity $user = null): bool
    {
        $user = $user ?? auth()->user()->toDomain();

        $allowedRoleIds = new RoleIdCollection(
            $this->resolver->resolveIds($this->rules['roles'] ?? [])
        );

        return $user->hasAnyRole($allowedRoleIds);
    }
}
