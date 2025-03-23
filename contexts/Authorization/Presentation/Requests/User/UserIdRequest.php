<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Requests\User;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class UserIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
