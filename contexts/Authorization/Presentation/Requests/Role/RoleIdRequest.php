<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Role;

use Contexts\Shared\Presentation\BaseFormRequest;

class RoleIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
