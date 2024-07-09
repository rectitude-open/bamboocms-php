<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\Requests\AdministratorPermission;

use App\Http\Requests\BaseFormRequest;

class IndexRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            ...$this->baseIndexRules(),
            'name' => ['string', 'max:255'],
        ];
    }
}
