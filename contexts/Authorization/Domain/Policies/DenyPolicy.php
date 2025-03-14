<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Policies;

use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;

/**
 * Default policy that always denies access when no policy configuration is found
 */
class DenyPolicy implements BasePolicy
{
    protected array $rules = [];

    public function evaluate(?UserIdentity $user = null): bool
    {
        // Always deny access by default
        return false;
    }

    public function withRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }
}
