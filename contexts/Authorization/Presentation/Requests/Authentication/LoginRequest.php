<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Authentication;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }
}
