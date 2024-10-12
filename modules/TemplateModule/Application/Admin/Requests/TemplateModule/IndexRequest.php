<?php

declare(strict_types=1);

namespace Modules\TemplateModule\Application\Admin\Requests\TemplateModule;

use App\Http\Requests\BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    public function filterRules(): array
    {
        return [
            'id' => ['integer', 'gte:1'],
            'string' => ['string', 'max:255'],
            'integer' => ['integer', 'gte:1'],
            'created_at' => ['array', 'size:2'],
        ];
    }
}
