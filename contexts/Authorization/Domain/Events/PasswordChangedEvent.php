<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Events;

use Contexts\Authorization\Domain\Models\UserId;
use Illuminate\Foundation\Events\Dispatchable;

class PasswordChangedEvent
{
    use Dispatchable;

    public function __construct(
        private readonly UserId $userId,
    ) {}

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
