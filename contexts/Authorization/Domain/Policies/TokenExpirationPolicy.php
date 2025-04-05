<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Policies;

use Carbon\CarbonImmutable;

class TokenExpirationPolicy
{
    public static function resolveExpiration(bool $remember): CarbonImmutable
    {
        return $remember ?
            CarbonImmutable::now()->addWeeks(2) :
            CarbonImmutable::now()->addDay();
    }
}
