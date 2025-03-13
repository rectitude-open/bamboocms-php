<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Policies;

use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleViewer;
use Contexts\ArticlePublishing\Domain\Models\ArticleVisibility;

class VisibilityPolicy
{
    public function __construct(
        private ArticleViewer $articleViewer
    ) {}

    public function fromArticle(Article $article)
    {
        $attributes = [
            'id' => $article->getId()->getValue(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'status' => $article->getStatus()->getValue(),
            'categories' => $article->getCategories()->map(fn ($category) => [
                'id' => $category->getId(),
                'label' => $category->getLabel(),
            ])->toArray(),
            'authorId' => $article->getAuthorId()->getValue(),
            'created_at' => $article->getCreatedAt(),
            'updated_at' => $article->getUpdatedAt(),
        ];

        if (! $this->canSeeUpdatedAt()) {
            unset($attributes['updated_at']);
        }

        return new ArticleVisibility(
            $attributes['id'],
            $attributes['title'],
            $attributes['body'],
            $attributes['status'],
            $attributes['categories'],
            $attributes['authorId'],
            $attributes['created_at'],
            $attributes['updated_at'] ?? null,
        );
    }

    private function canSeeUpdatedAt(): bool
    {
        if ($this->articleViewer->isAdmin()) {
            return true;
        }

        return false;
    }
}
