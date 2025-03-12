<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Models;

use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Shared\ValueObjects\IntId;

class AuthorId extends IntId
{
    public static function fromUserId(UserId $userId): self
    {
        return self::fromInt($userId->getValue());
    }
}
