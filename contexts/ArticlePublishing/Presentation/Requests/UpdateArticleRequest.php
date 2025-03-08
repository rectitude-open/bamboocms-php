<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use App\Http\Requests\BaseFormRequest;

class UpdateArticleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'gt:0'],
            'title' => ['string', 'max:255'],
            'body' => ['string', 'max:20000'],
            'status' => ['string', 'in:draft,published'],
            'created_at' => ['date', 'date_format: Y-m-d H:i:s'],
        ];
    }
}
