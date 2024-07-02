<?php

declare(strict_types=1);

namespace App\Http\Requests;

class BulkResourceRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'gte:1'],
        ];
    }
}
