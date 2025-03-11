<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Services;

use Contexts\Authorization\Domain\Policies\BasePolicy;

class ContextPolicyBuilder
{
    public function __construct(
        private array $config,
    ) {}

    public function action(string $action): BasePolicy
    {
        $handlerClass = $this->config['context_default']['handler'];

        if (isset($this->config['actions'][$action])) {
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
