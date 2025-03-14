<?php

declare(strict_types=1);

namespace Contexts\Authorization\Application\Coordinators;

use Contexts\Authorization\Contracts\V1\Services\GlobalPermissionService;
use Contexts\Authorization\Domain\Services\PolicyFactory;

class GlobalPermissionServiceCoordinator implements GlobalPermissionService
{
    public function __construct(
        private PolicyFactory $policyFactory
    ) {}

    public function checkPermission(string $context, string $action): bool
    {
        $policy = $this->policyFactory
            ->forContext($context)
            ->action($action);

        return $policy->evaluate();
    }
}
