<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation\Requests\Concerns;

trait HandlesRouteId
{
    protected function bindRouteId(): void
    {
        $this->route('id') && $this->merge(['id' => $this->route('id')]);
    }

    protected function idRule(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
        ];
    }
}
