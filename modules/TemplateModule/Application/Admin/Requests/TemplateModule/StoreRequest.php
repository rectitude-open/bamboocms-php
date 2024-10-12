<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Application\Admin\Requests\TemplateModule;

use App\Http\Requests\BaseFormRequest;

class StoreRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'string' => ['required', 'string', 'max:255'],
            'integer' => ['integer', 'gte:1'],
        ];
    }
}
