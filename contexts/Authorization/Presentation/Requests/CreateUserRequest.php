<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class CreateUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'min:4', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'display_name' => ['required', 'string', 'max:255'],
            'created_at' => ['date', 'date_format: Y-m-d H:i:s'],
        ];
    }
}
