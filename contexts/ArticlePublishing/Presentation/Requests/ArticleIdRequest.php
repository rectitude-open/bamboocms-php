<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class ArticleIdRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->idRule('articles');
    }
}
