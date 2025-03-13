<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * @var \Contexts\ArticlePublishing\Domain\Models\ArticleVisibility $article
         */
        $article = $this->resource;

        return [
            'id' => (int) $article->getId(),

            'title' => (string) $article->getTitle(),
            'body' => (string) $article->getbody(),
            'status' => (string) $article->getStatus(),
            'categories' => $article->getCategories(),
            'author_id' => (int) $article->getAuthorId(),
            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->when(
                (bool) $article->getUpdatedAt(),
                $article->getUpdatedAt()?->format('Y-m-d H:i:s')
            ),
        ];
    }
}
