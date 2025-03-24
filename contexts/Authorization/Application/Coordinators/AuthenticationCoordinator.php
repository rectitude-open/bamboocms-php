<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use Carbon\CarbonImmutable;
use Contexts\Authorization\Application\DTOs\User\CreateUserDTO;
use Contexts\Authorization\Application\DTOs\User\GetUserListDTO;
use Contexts\Authorization\Application\DTOs\User\UpdateUserDTO;
use Contexts\Authorization\Domain\Factories\UserIdentityFactory;
use Contexts\Authorization\Domain\Repositories\UserRepository;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Shared\Application\BaseCoordinator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Contexts\Authorization\Application\DTOs\Authentication\LoginDTO;

class AuthenticationCoordinator extends BaseCoordinator
{
    public function __construct(
        private UserRepository $userRepository,
        private UserIdentityFactory $factory
    ) {
    }

    public function login(LoginDTO $dto)
    {
        $user = $this->userRepository->getByEmail($dto->email);
        $user->authenticate($dto->password);

        $token = $this->userRepository->generateLoginToken($user);

        $this->dispatchDomainEvents($user);

        return [
            'user' => [
                'id' => $user->getId()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'display_name' => $user->getDisplayName(),
            ],
            'token' => $token,
        ];
    }
}
