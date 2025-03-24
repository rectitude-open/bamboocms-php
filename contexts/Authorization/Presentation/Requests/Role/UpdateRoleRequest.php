<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Role;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class UpdateRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
            'label' => ['string', 'max:255'],
            'status' => ['string', 'in:suspended,active'],
            'created_at' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
