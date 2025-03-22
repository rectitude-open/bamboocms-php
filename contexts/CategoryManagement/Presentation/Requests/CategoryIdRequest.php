<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use Contexts\Shared\Presentation\BaseFormRequest;

class CategoryIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
