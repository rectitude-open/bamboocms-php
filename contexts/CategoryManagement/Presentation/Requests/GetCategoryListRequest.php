<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class GetCategoryListRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer', 'gt:0'],
            'label' => ['string', 'max:255'],
            'status' => ['string', 'in:subspended,active'],
            'created_at_range' => ['array', 'size:2'],
            'created_at_range.*' => ['date'],
            'page' => ['integer', 'gt:0'],
            'per_page' => ['integer', 'gt:0'],
        ];
    }
}
