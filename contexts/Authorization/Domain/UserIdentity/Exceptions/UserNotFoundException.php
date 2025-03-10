<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Exceptions;

use Contexts\Shared\Exceptions\NotFoundException;

class UserNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct('User with id :id not found', ['id' => $id]);
    }
}
