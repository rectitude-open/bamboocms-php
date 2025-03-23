<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\User;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class GetUserListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer', 'gt:0'],
            'display_name' => ['string', 'max:255'],
            'email' => ['email', 'max:255'],
            'status' => ['string', 'in:subspended,active'],
            'created_at_range' => ['array', 'size:2'],
            'created_at_range.*' => ['date'],
            'current_page' => ['integer', 'gt:0'],
            'per_page' => ['integer', 'gt:0'],
        ];
    }
}
