<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Role\Events;

use Contexts\Authorization\Domain\Role\Models\RoleId;
use Illuminate\Foundation\Events\Dispatchable;

class RoleCreatedEvent
{
    use Dispatchable;

    public function __construct(
        private readonly RoleId $roleId,
    ) {
    }

    public function getRoleId(): RoleId
    {
        return $this->roleId;
    }
}
