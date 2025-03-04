<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Repositories;

use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;

class ArticleRepository
{
    public function create(Article $article): Article
    {
        $record = ArticleRecord::create([
            'title' => $article->getTitle(),
            'body' => $article->getbody(),
            'status' => ArticleRecord::mapStatusToRecord($article->getStatus()),
            'created_at' => $article->getCreatedAt(),
        ]);

        return $record->toDomain($article->getDomainEvents());
    }

    public function getById(ArticleId $articleId): Article
    {
        $record = ArticleRecord::findOrFail($articleId->value);

        return $record->toDomain();
    }

    public function update(Article $article): Article
    {
        $record = ArticleRecord::findOrFail($article->id->value);

        $record->update([
            'title' => $article->getTitle(),
            'body' => $article->getbody(),
            'status' => ArticleRecord::mapStatusToRecord($article->getStatus()),
            'created_at' => $article->getCreatedAt(),
        ]);

        return $record->toDomain($article->getDomainEvents());
    }
}
