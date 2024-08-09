<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ValidFilterValue;
use App\Rules\ValidSorting;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseIndexRequest extends FormRequest
{
    abstract public function filterRules(): array;

    protected array $casts = [
        'id' => 'int',
        'per_page' => 'int',
        'current_page' => 'int',
        'pagination' => 'boolean',
        'sorting' => 'json',
    ];

    protected function defaultIndexRules()
    {
        return [
            'id' => ['integer', 'gte:1'],
            'per_page' => ['integer', 'gte:1', 'lte:100'],
            'current_page' => ['integer', 'gte:1'],
            'pagination' => ['in:true,false'],
            'sorting' => ['nullable', 'string', new ValidSorting],
            'filters' => ['array'],
            'filters.*.id' => ['required', 'string'],
            'filters.*.value' => ['required', (new ValidFilterValue($this->filterRules()))],
        ];
    }

    public function rules(): array
    {
        return [
            ...$this->defaultIndexRules(),
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('filters')) {
            $inputFilters = $this->input('filters');

            if (is_array($inputFilters)) {
                $filters = $inputFilters;
            } else {
                $filters = json_decode($inputFilters, true);
            }

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['filters' => $filters]);
            }
        }
    }

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

    protected function castValue($value, $type)
    {
        switch ($type) {
            case 'int':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
