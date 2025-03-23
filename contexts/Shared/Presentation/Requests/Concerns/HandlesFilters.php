<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation\Requests\Concerns;

trait HandlesFilters
{
    protected function filtersRule(): array
    {
        return [
            'filters' => ['sometimes', 'array'],
            'filters.*.id' => ['required_with:filters', 'string'],
            'filters.*.value' => ['required_with:filters'],
        ];
    }

    protected function filtersCast(): void
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
}
