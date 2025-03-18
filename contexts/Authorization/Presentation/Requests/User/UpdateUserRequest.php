<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
            'email' => ['email', 'max:255'],
            'display_name' => ['string', 'max:255'],
            'status' => ['string', 'in:subspended,active'],
            'created_at' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
