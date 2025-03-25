<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Gateway;

interface AuthorizationGateway
{
    public function canPerformAction(string $action): bool;
}
