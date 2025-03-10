<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateRolesRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => $this->idRule(),
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['required', 'integer', 'gt:0'],
        ];
    }
}
