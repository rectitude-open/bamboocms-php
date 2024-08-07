<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        parent::prepareForValidation();

        $this->castInputValues();
    }

    protected function castInputValues()
    {
        $casts = [
            'id' => 'int',
            'per_page' => 'int',
            'current_page' => 'int',
            'pagination' => 'boolean',
            'sorting' => 'sorting',
        ];

        foreach ($casts as $field => $type) {
            if ($this->has($field)) {
                $this->merge([$field => $this->castValue($this->input($field), $type)]);
            }
        }
    }

    public function baseIndexRules(): array
    {
        return [
            'id' => ['integer', 'gte:1'],
            'per_page' => ['integer', 'gte:1', 'lte:100'],
            'current_page' => ['integer', 'gte:1'],
            'pagination' => ['in:true,false'],
            'start_date' => ['date_format:Y-m-d H:i:s,Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['date_format:Y-m-d H:i:s,Y-m-d', 'after_or_equal:start_date'],
            'start_time' => ['date_format:Y-m-d H:i:s,Y-m-d', 'before_or_equal:end_time'],
            'end_time' => ['date_format:Y-m-d H:i:s,Y-m-d', 'after_or_equal:start_time'],
            'sorting' => ['nullable', 'array'],
            'sorting.*.id' => ['required', 'string'],
            'sorting.*.desc' => ['required', 'boolean'],
        ];
    }

    protected function castValue($value, $type)
    {
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'sorting':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
