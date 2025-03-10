<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Events;

use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Illuminate\Foundation\Events\Dispatchable;

class RoleRemovedEvent
{
    use Dispatchable;

    public function __construct(
        private readonly UserId $userId,
        private readonly RoleId $roleId
    ) {}

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getRoleId(): RoleId
    {
        return $this->roleId;
    }
}
