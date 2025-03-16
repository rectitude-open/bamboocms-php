<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use App\Http\Coordinators\BaseCoordinator;
use Contexts\Authorization\Contracts\V1\DTOs\CurrentUserDTO;
use Contexts\Authorization\Contracts\V1\Services\CurrentUserService;
use Contexts\Authorization\Domain\Repositories\RoleRepository;
use Contexts\Authorization\Domain\Repositories\UserRepository;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;

class CurrentUserServiceCoordinator extends BaseCoordinator implements CurrentUserService
{
    public function __construct(
        private UserRepository $userRepository,
        private RoleRepository $roleRepository,
    ) {}

    public function getCurrentUser(): CurrentUserDTO
    {
        $user = $this->userRepository->getById(
            UserId::fromInt(auth()->id())
        );

        $userRoles = $user->getRoleIdCollection();
        $roles = $this->roleRepository->getByIds($userRoles->getIdsArray());

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
