<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->route('id') && $this->merge(['id' => $this->route('id')]);
        $this->autoCast();
        $this->filtersCast();
        $this->sortingCast();
    }

    private function filtersCast()
    {
        if ($this->has('filters')) {
            $inputFilters = $this->input('filters');

            $filters = $inputFilters[0];
            $filters = json_decode($inputFilters[0], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['filters' => $filters]);
            }
        }
    }

    private function sortingCast()
    {
        if ($this->has('sorting')) {
            $inputSorting = $this->input('sorting');

            $sorting = $inputSorting[0];
            $sorting = json_decode($inputSorting[0], true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['sorting' => $sorting]);
            }
        }
    }

    protected function idRule(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
        ];
    }

    protected function filtersRule(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filters.*.id' => ['required_with:filters', 'string'],
            'filters.*.value' => ['required_with:filters'],
        ];
    }

    protected function sortingRule(): array
    {
        return [
            'sorting' => ['sometimes', 'array'],
            'sorting.*.id' => ['required_with:sorting', 'string'],
            'sorting.*.desc' => ['required_with:sorting', 'boolean'],
        ];
    }

    private function autoCast(): void
    {
        $casts = $this->inferCastsFromRules();

        $this->merge(
            collect($this->all())
                ->mapWithKeys(fn ($value, $key) => [
                    $key => $this->castValue(
                        $key,
                        $value,
                        $casts[$key] ?? null
                    ),
                ])
                ->toArray()
        );
    }

    private function inferCastsFromRules(): array
    {
        return collect($this->rules())
            ->mapWithKeys(fn ($rules, $key) => [
                $key => $this->parseTypeFromRules((array) $rules),
            ])
            ->filter()
            ->toArray();
    }

    private function parseTypeFromRules(array $rules): ?string
    {
        foreach ($rules as $rule) {
            if ($type = $this->matchPrimitiveType($rule)) {
                return $type;
            }
        }

        return null;
    }

    private function matchPrimitiveType($rule): ?string
    {
        return match (true) {
            $rule === 'integer' => 'int',
            $rule === 'boolean' => 'bool',
            $rule === 'numeric' => 'float',
            $rule === 'array' => 'array',
            default => null
        };
    }

    private function castValue(string $key, mixed $value, ?string $type): mixed
    {
        if ($type === null || $value === null) {
            return $value;
        }

        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array' => (array) $value,
            default => $value
        };
    }
}
