<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use Contexts\Shared\Presentation\BaseFormRequest;

class ArticleIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
