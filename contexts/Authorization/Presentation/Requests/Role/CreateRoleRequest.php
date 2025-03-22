<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Role;

use Contexts\Shared\Presentation\BaseFormRequest;

class CreateRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'created_at' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
