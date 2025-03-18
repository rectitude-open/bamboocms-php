<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class CreateCategoryRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'created_at' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
