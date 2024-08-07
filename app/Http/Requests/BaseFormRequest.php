<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ValidSorting;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    protected array $casts = [
        'id' => 'int',
        'per_page' => 'int',
        'current_page' => 'int',
        'pagination' => 'boolean',
        'sorting' => 'sorting',
    ];

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        return $this->castInputValues($data);
    }

    protected function castInputValues(array $data)
    {
        foreach ($this->casts as $field => $type) {
            if (isset($data[$field])) {
                $data[$field] = $this->castValue($data[$field], $type);
            }
        }

        return $data;
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
            'sorting' => ['nullable', 'string', new ValidSorting],
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
