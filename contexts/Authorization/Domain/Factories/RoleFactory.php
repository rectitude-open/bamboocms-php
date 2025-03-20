<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Factories;

use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Role\Events\RoleCreatedEvent;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Domain\Services\RoleLabelUniquenessService;

class RoleFactory
{
    public function __construct(
        private readonly RoleLabelUniquenessService $roleLabelUniquenessService
    ) {
    }

    public function create(
        RoleId $id,
        string $label,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): Role {
        $this->roleLabelUniquenessService->ensureUnique($label);

        $role = Role::createFromFactory(
            $id,
            $label,
            RoleStatus::active(),
            $created_at,
            $updated_at,
        );
        $role->recordEvent(new RoleCreatedEvent($role->id));

        return $role;
    }

    public function reconstitute(
        RoleId $id,
        string $label,
        RoleStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        array $events = []
    ): Role {
        return Role::createFromFactory($id, $label, $status, $created_at, $updated_at, $events);
    }
}
