<?php

declare(strict_types=1);

namespace Contexts\Shared\Exceptions;

abstract class DomainException extends \DomainException
{
    public function __construct(
        string $message,
        array $params = [],
        int $code = 0,
    ) {
        parent::__construct(trans($message, $params), $code);
    }
}
