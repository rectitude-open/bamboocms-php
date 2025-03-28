<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\BaseRequest;

class GetArticleListRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer', 'gt:0'],
            'title' => ['string', 'max:255'],
            'status' => ['string', 'in:draft,published'],
            'category_id' => ['integer', 'gt:0'],
            'author_id' => ['integer', 'gt:0'],
            'created_at_range' => ['array', 'size:2'],
            'created_at_range.*' => ['date'],
            'current_page' => ['integer', 'gt:0'],
            'per_page' => ['integer', 'gt:0'],
        ];
    }
}
