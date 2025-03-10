<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Role;

use App\Http\Requests\BaseFormRequest;

class RoleIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
