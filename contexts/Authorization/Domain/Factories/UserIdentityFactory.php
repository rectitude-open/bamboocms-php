<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Factories;

use Contexts\Authorization\Domain\Services\UserEmailUniquenessService;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Domain\UserIdentity\Events\UserCreatedEvent;

class UserIdentityFactory
{
    public function __construct(
        private readonly UserEmailUniquenessService $userEmailUniquenessService
    ) {
    }

    public function create(
        UserId $id,
        Email $email,
        Password $password,
        string $display_name,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): UserIdentity {
        $this->userEmailUniquenessService->ensureUnique($email->getValue());

        $user = UserIdentity::createFromFactory($id, $email, $password, $display_name, UserStatus::active(), $created_at, $updated_at);
        $user->recordEvent(new UserCreatedEvent($user->id));

        return $user;
    }
}
