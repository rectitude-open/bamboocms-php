<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class ArticleIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->idRule();
    }
}
