<?php

declare(strict_types=1);

namespace Contexts\Authorization\Contracts\V1\Services;

use Contexts\Authorization\Contracts\V1\DTOs\CurrentUserDTO;

interface CurrentUserService
{
    public function getCurrentUser(): CurrentUserDTO;
}
