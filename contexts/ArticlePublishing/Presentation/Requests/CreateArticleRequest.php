<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class CreateArticleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['string', 'max:20000'],
        ];
    }
}
