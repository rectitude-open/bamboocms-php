<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class ChangePasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => $this->idRule(),
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'new_password_confirmation' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }
}
