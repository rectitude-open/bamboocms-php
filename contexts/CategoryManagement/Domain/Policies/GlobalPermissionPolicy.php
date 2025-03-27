<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Domain\Policies;

use App\Exceptions\BizException;
use Contexts\CategoryManagement\Domain\Gateway\AuthorizationGateway;
use Contexts\Shared\Contracts\BaseAuthorizationPolicy;

class GlobalPermissionPolicy implements BaseAuthorizationPolicy
{
    public function __construct(private string $action)
    {
    }

    public static function canPerform(string $action)
    {
        return new self($action);
    }

    public function check(): void
    {
        $authorizationGateway = app(AuthorizationGateway::class);

        if (! $authorizationGateway->canPerformAction($this->action)) {
            throw BizException::make('You are not authorized to perform this action')->code(403);
        }
    }
}
