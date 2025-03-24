<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Exceptions;

use App\Exceptions\BizException;

class AuthenticationFailureException extends BizException
{
    public static function make(string $message = ''): self
    {
        $message = $message ?: 'Invalid login credentials or account access restricted';
        return (new static($message))->code(401);
    }
}
