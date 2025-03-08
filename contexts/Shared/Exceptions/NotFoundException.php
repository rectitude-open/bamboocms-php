<?php

declare(strict_types=1);

namespace Contexts\Shared\Exceptions;

use Exception;

abstract class NotFoundException extends Exception
{
    public function __construct(
        string $message,
        array $params = [],
    ) {
        parent::__construct(trans($message, $params));
    }
}
