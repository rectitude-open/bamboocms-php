<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation\Requests\Concerns;

trait HandlesSorting
{
    protected function sortingRule(): array
    {
        return [
            'sorting' => ['sometimes', 'array'],
            'sorting.*.id' => ['required_with:sorting', 'string'],
            'sorting.*.desc' => ['required_with:sorting', 'boolean'],
        ];
    }

    protected function sortingCast(): void
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
}
