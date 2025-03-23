<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Role;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class RoleIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
