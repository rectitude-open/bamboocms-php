<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

use App\Exceptions\BizException;
use Contexts\Authorization\Domain\Repositories\UserRepository;

class UserEmailUniquenessService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function ensureUnique(string $email)
    {
        if ($this->userRepository->existsByEmail($email)) {
            throw BizException::make('User email already exists: :email')
                ->with('email', $email);
        }
    }
}
