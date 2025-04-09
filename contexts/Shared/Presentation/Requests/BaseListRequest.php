<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\Concerns\HandlesFilters;
use Contexts\Shared\Presentation\Requests\Concerns\HandlesSorting;

abstract class BaseListRequest extends BaseRequest
{
    use HandlesFilters;
    use HandlesSorting;

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->filtersCast();
        $this->sortingCast();
    }

    protected function paginationRule(): array
    {
        return [
            'current_page' => ['integer', 'gt:0'],
            'per_page' => ['integer', 'gt:0'],
        ];
    }
}
