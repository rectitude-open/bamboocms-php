<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\Requests\AdministratorRole;

use App\Http\Requests\BaseIndexRequest;

class IndexRequest extends BaseIndexRequest
{
    public function filterRules(): array
    {
        return [
            'id' => ['integer', 'gte:1'],
            'name' => ['string', 'max:255'],
            'created_at' => ['array', 'size:2'],
        ];
    }
}
