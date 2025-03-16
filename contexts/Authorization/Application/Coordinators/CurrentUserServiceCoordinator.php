<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Contexts\Authorization\Contracts\V1\DTOs\CurrentUserDTO;
use Contexts\Authorization\Contracts\V1\Services\CurrentUserService;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Infrastructure\Persistence\RolePersistence;
use Contexts\Authorization\Infrastructure\Persistence\UserPersistence;

class CurrentUserServiceCoordinator extends BaseCoordinator implements CurrentUserService
{
    public function __construct(
        private UserPersistence $userPersistence,
        private RolePersistence $rolePersistence,
    ) {}

    public function getCurrentUser(): CurrentUserDTO
    {
        $user = $this->userPersistence->getById(
            UserId::fromInt(auth()->id())
        );

        $userRoles = $user->getRoleIdCollection();
        $roles = $this->rolePersistence->getByIds($userRoles->getIdsArray());

        return new CurrentUserDTO(
            $user->getId()->getValue(),
            $user->getDisplayName(),
            $user->getEmail()->getValue(),
            $roles->map(fn ($role) => [
                'id' => $role->getId()->getValue(),
                'label' => $role->getLabel(),
            ])->toArray()
        );
    }
}
