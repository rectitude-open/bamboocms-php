<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Adapters;

use Contexts\ArticlePublishing\Domain\Gateway\AuthorizationGateway;
use Contexts\Authorization\Domain\Services\PolicyFactory;

class AuthorizationAdapter implements AuthorizationGateway
{
    public function __construct(
        private PolicyFactory $policyFactory
    ) {}

    public function canPerformAction(string $action): bool
    {
        $policy = $this->policyFactory
            ->forContext('article_publishing')
            ->action($action);

        return $policy->evaluate();
    }
}
