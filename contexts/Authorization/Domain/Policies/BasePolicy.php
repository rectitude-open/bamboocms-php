<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Policies;

use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;

interface BasePolicy
{
    public function evaluate(?UserIdentity $user = null): bool;

    public function withRules(array $rules): self;
}
