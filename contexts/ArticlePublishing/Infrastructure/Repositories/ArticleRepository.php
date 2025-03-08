<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Repositories;

use Contexts\ArticlePublishing\Domain\Exceptions\ArticleNotFoundException;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

        return $record->toDomain($article->getEvents());
    }

    public function getById(ArticleId $articleId): Article
    {
        try {
            $record = ArticleRecord::findOrFail($articleId->getValue());
        } catch (ModelNotFoundException $e) {
            throw new ArticleNotFoundException($articleId->getValue());
        }

        return $record->toDomain();
    }

    public function update(Article $article): Article
    {
        try {
            $record = ArticleRecord::findOrFail($article->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new ArticleNotFoundException($article->getId()->getValue());
        }

        $record->update([
            'title' => $article->getTitle(),
            'body' => $article->getbody(),
            'status' => ArticleRecord::mapStatusToRecord($article->getStatus()),
            'created_at' => $article->getCreatedAt(),
        ]);

        return $record->toDomain($article->getEvents());
    }

    public function paginate(int $page = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator
    {
        $paginator = ArticleRecord::query()->search($criteria)->paginate($perPage, ['*'], 'page', $page);

        $paginator->getCollection()->transform(function ($record) {
            return $record->toDomain();
        });

        return $paginator;
    }

    public function delete(Article $article): void
    {
        try {
            $record = ArticleRecord::findOrFail($article->getId()->getValue());
        } catch (ModelNotFoundException $e) {
            throw new ArticleNotFoundException($article->getId()->getValue());
        }
        $record->update(['status' => ArticleRecord::mapStatusToRecord(ArticleStatus::deleted())]);
        $record->delete();
    }
}
