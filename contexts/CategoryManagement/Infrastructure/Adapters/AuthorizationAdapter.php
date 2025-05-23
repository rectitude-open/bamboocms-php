<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure\Adapters;

use Contexts\Authorization\Contracts\V1\Services\GlobalPermissionService;
use Contexts\CategoryManagement\Domain\Gateway\AuthorizationGateway;

class AuthorizationAdapter implements AuthorizationGateway
{
    public function __construct(
        private GlobalPermissionService $globalPermissionService,
    ) {}

    public function canPerformAction(string $action): bool
    {
        return $this->globalPermissionService->checkPermission('category_management', $action);
    }
}
