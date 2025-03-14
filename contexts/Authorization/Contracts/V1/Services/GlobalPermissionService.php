<?php

declare(strict_types=1);

namespace Contexts\Authorization\Contracts\V1\Services;

interface GlobalPermissionService
{
    public function checkPermission(string $context, string $action): bool;
}
