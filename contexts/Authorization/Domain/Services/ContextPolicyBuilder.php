<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

use Contexts\Authorization\Domain\Policies\BasePolicy;
use Contexts\Authorization\Domain\Policies\DenyPolicy;

class ContextPolicyBuilder
{
    public function __construct(
        private array $config,
    ) {}

    public function action(string $action): BasePolicy
    {
        // Default handler is DenyPolicy if context_default is not set or missing handler
        $defaultHandler = $this->config['context_default']['handler'] ?? DenyPolicy::class;
        $handlerClass = $defaultHandler;

        // Override with action specific handler if available
        if (isset($this->config['actions'][$action]['handler'])) {
            $handlerClass = $this->config['actions'][$action]['handler'];
        }

        $policy = app($handlerClass);

        return $policy->withRules(
            array_merge(
                $this->config['context_default']['rules'] ?? [],
                $this->config['actions'][$action]['rules'] ?? []
            )
        );
    }
}
