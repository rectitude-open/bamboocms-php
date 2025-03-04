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
         * @var \Contexts\ArticlePublishing\Domain\Models\Article $article
         */
        $article = $this->resource;

        return [
            'id' => (int) $article->getId()->getValue(),

            'title' => (string) $article->getTitle(),
            'body' => (string) $article->getbody(),
            'status' => (string) $article->getStatus()->getValue(),

            'created_at' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $article->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
