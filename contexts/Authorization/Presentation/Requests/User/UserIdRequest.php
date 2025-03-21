<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UserIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
