<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Role\Exceptions;

use Contexts\Shared\Exceptions\NotFoundException;

class RoleNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct('Role with id :id not found', ['id' => $id]);
    }
}
