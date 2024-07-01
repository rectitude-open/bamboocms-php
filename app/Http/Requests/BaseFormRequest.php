<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
    }

    public function baseIndexRules(): array
    {
        return [
            'id' => ['integer', 'gte:1'],
            'sort' => ['string', 'min:2', 'max:255'],
            'order' => ['string', 'regex:/^(desc|asc)$/i'],
            'per_page' => ['integer', 'gte:1', 'lte:100'],
            'current_page' => ['integer', 'gte:1'],
            'pagination' => ['in:true,false'],
            'with_trashed' => ['in:true,false'],
            'start_date' => ['date_format:Y-m-d H:i:s,Y-m-d', 'before_or_equal:end_date'],
            'end_date' => ['date_format:Y-m-d H:i:s,Y-m-d', 'after_or_equal:start_date'],
            'start_time' => ['date_format:Y-m-d H:i:s,Y-m-d', 'before_or_equal:end_time'],
            'end_time' => ['date_format:Y-m-d H:i:s,Y-m-d', 'after_or_equal:start_time'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validatedData = parent::validated($key, $default);

        $casts = [
            'id' => 'int',
            'page_size' => 'int',
            'current' => 'int',
            'pagination' => 'boolean',
            'with_trashed' => 'boolean',
        ];

        foreach ($casts as $field => $type) {
            if (isset($validatedData[$field])) {
                $validatedData[$field] = $this->castValue($validatedData[$field], $type);
            }
        }

        return $validatedData;
    }

    protected function castValue($value, $type)
    {
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            default:
                return $value;
        }
    }
}
