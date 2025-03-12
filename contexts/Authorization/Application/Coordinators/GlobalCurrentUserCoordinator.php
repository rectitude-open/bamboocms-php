<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;

class GlobalCurrentUserCoordinator extends BaseCoordinator
{
    public function getId(): UserId
    {
        return UserId::fromInt(auth()->id());
    }
}
