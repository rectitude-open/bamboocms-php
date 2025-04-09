<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Requests;

use Contexts\Shared\Presentation\Requests\BaseListRequest;

class GetArticleListRequest extends BaseListRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer', 'gt:0'],
            'title' => ['string', 'max:255'],
            'status' => ['string', 'in:draft,published'],
            'category_id' => ['integer', 'gt:0'],
            'author_id' => ['integer', 'gt:0'],
            'created_at' => ['array', 'size:2'],
            'created_at.*' => ['date_format:Y-m-d'],
            ...$this->paginationRule(),
            ...$this->filtersRule(),
            ...$this->sortingRule(),
        ];
    }
}
