<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\AuthorizationGateway;
use Contexts\Authorization\Contracts\V1\Services\GlobalPermissionService;

class AuthorizationAdapter implements AuthorizationGateway
{
    public function __construct(
        private GlobalPermissionService $globalPermissionService,
    ) {}

    public function canPerformAction(string $action): bool
    {
        return $this->globalPermissionService->checkPermission('article_publishing', $action);
    }
}
