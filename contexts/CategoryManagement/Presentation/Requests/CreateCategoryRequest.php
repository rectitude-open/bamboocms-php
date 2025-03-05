<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class CreateCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['string', 'max:20000'],
            'status' => ['required', 'string', 'in:draft,published'],
            'created_at' => ['date', 'date_format: Y-m-d H:i:s'],
        ];
    }
}
