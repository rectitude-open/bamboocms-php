<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class CreateArticleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['string', 'max:20000'],
            'status' => ['required', 'string', 'in:draft,published'],
            'category_ids' => ['array'],
            'category_ids.*' => ['integer', 'gt:0'],
            'author_id' => ['integer', 'gt:0'],
            'created_at' => ['date', 'date_format:Y-m-d H:i:s'],
        ];
    }
}
