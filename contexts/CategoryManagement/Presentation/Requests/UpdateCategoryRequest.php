<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class UpdateCategoryRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
            'label' => ['string', 'max:255'],
            'status' => ['string', 'in:subspended,active'],
            'created_at' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
