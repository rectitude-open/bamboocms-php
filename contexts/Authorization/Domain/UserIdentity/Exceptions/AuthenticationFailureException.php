<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Exceptions;

use App\Exceptions\BizException;

final class AuthenticationFailureException extends BizException
{
    public static function make(string $message = ''): static
    {
        $message = $message ?: 'Invalid login credentials or account access restricted';

        /** @var static */
        return (new self($message))->code(401);
    }
}
