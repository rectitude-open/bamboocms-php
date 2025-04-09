<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\User;

use Contexts\Shared\Presentation\Requests\BaseListRequest;

class GetUserListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer', 'gt:0'],
            'display_name' => ['string', 'max:255'],
            'email' => ['email', 'max:255'],
            'status' => ['string', 'in:suspended,active'],
            'created_at' => ['array', 'size:2'],
            'created_at.*' => ['date_format:Y-m-d'],
            ...$this->paginationRule(),
            ...$this->filtersRule(),
            ...$this->sortingRule(),
        ];
    }
}
