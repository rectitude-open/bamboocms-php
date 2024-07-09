<?php

declare(strict_types=1);

namespace Modules\AdministratorRole\Application\Admin\Requests\AdministratorPermission;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
    }

    public function rules(): array
    {
        return [
            'id' => ['integer', 'gte: 1'],
            'name' => ['string', 'max:255', 'unique:administrator_permissions,name,'.$this->id],
        ];
    }
}
