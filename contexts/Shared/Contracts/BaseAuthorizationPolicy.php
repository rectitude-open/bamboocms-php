<?php

declare(strict_types=1);

namespace Contexts\Shared\Contracts;

interface BaseAuthorizationPolicy
{
    public function check(): void;
}
