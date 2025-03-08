<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->route('id') && $this->merge(['id' => $this->route('id')]);
    }

    protected function idRule(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
        ];
    }
}
