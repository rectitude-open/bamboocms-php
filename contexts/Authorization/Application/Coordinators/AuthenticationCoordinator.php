<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use Contexts\Authorization\Application\DTOs\Authentication\LoginDTO;
use Contexts\Authorization\Domain\Policies\TokenExpirationPolicy;
use Contexts\Authorization\Domain\Repositories\UserRepository;
use Contexts\Shared\Application\BaseCoordinator;

class AuthenticationCoordinator extends BaseCoordinator
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function login(LoginDTO $dto)
    {
        $user = $this->userRepository->getByEmailOrThrowAuthFailure($dto->email);
        $user->authenticate($dto->password);

        $expiresAt = TokenExpirationPolicy::resolveExpiration($dto->remember);
        $token = $this->userRepository->generateLoginToken($user, $expiresAt);

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

    public function me()
    {
        $user = $this->userRepository->getCurrentUser();

        return [
            'user' => [
                'id' => $user->getId()->getValue(),
                'email' => $user->getEmail()->getValue(),
                'display_name' => $user->getDisplayName(),
            ],
        ];
    }
}
