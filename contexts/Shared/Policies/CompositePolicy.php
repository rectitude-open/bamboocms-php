<?php

declare(strict_types=1);

namespace Contexts\Shared\Policies;

use App\Exceptions\BizException;
use App\Exceptions\SysException;
use Contexts\Shared\Contracts\BaseAuthorizationPolicy;

class CompositePolicy implements BaseAuthorizationPolicy
{
    public const MODE_ANY = 'any';

    public const MODE_ALL = 'all';

    public function __construct(
        private array $policies,
        private string $mode = self::MODE_ANY
    ) {
        $this->validatePolicies();
    }

    public static function anyOf(array $policies): self
    {
        return new self($policies, self::MODE_ANY);
    }

    public static function allOf(array $policies): self
    {
        return new self($policies, self::MODE_ALL);
    }

    public function check(): void
    {
        $exceptions = [];

        foreach ($this->policies as $policy) {
            try {
                $policy->check();

                if ($this->mode === self::MODE_ANY) {
                    return;
                }
            } catch (BizException $e) {
                if ($this->mode === self::MODE_ALL) {
                    throw $e;
                }
                $exceptions[] = $e;
            }
        }

        if ($this->mode === self::MODE_ANY && count($exceptions) > 0) {
            throw BizException::make('You are not authorized to perform this action')->code(403);
        }
    }

    private function validatePolicies(): void
    {
        foreach ($this->policies as $policy) {
            if (! $policy instanceof BaseAuthorizationPolicy) {
                throw SysException::make('Invalid policy type');
            }
        }
    }
}
