<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class CategoryIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
