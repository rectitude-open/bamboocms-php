<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

class PolicyFactory
{
    public function forContext(string $context): ContextPolicyBuilder
    {
        $config = config("policies.{$context}");

        return new ContextPolicyBuilder($config);
    }
}
