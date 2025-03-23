<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\Concerns\HandlesAutoCasting;
use Contexts\Shared\Presentation\Requests\Concerns\HandlesRouteId;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    use HandlesAutoCasting;
    use HandlesRouteId;

    abstract public function rules(): array;

    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        $this->bindRouteId();
        $this->autoCast();
    }
}
