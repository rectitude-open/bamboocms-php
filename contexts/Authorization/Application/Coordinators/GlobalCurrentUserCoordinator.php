<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Infrastructure\Repositories\UserRepository;
// TODO: ADD TEST
class GlobalCurrentUserCoordinator extends BaseCoordinator
{
    private ?UserIdentity $identity = null;

    public function getId(): UserId
    {
        return UserId::fromInt(auth()->id());
    }

    public function getUserIdentity(): UserIdentity
    {
        if (! $this->identity) {
            $this->identity = app(UserRepository::class)->getById(
                $this->getId()
            );
        }

        return $this->identity;
    }

    public function getRole(): RoleIdCollection
    {
        return $this->getUserIdentity()->getRoleIdCollection();
    }
}
