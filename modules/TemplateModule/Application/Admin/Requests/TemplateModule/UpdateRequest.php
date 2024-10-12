<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Application\Admin\Requests\TemplateModule;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
    }

    public function rules(): array
    {
        return [
            'id' => ['integer', 'gte: 1'],
            'string' => ['string', 'max:255'],
            'integer' => ['integer', 'gte:1'],
        ];
    }
}
