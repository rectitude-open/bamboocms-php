<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\Role;

use Contexts\Shared\Presentation\Requests\BaseListRequest;

class GetRoleListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer', 'gt:0'],
            'label' => ['string', 'max:255'],
            'status' => ['string', 'in:suspended,active'],
            'created_at_range' => ['array', 'size:2'],
            'created_at_range.*' => ['date'],
            'current_page' => ['integer', 'gt:0'],
            'per_page' => ['integer', 'gt:0'],
            ...$this->filtersRule(),
            ...$this->sortingRule(),
        ];
    }
}
