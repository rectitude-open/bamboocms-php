<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

class PolicyFactory
{
    public function forContext(string $context): ContextPolicyBuilder
    {
        $config = config("policies.{$context}") ?? [
            'context_default' => [
                'handler' => config('policies.default_handler', \Contexts\Authorization\Domain\Policies\DenyPolicy::class),
                'rules' => [],
            ],
            'actions' => [],
        ];

        return new ContextPolicyBuilder($config);
    }
}
