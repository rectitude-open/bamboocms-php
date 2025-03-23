<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class CategoryIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
