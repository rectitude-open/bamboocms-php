<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Persistence;

use Contexts\ArticlePublishing\Domain\Exceptions\ArticleNotFoundException;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Domain\Repositories\ArticleRepository;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ArticlePersistence implements ArticleRepository
{
    public function create(Article $article): Article
    {
        return DB::transaction(function () use ($article) {
            $record = ArticleRecord::create([
                'title' => $article->getTitle(),
                'body' => $article->getbody(),
                'status' => ArticleRecord::mapStatusToRecord($article->getStatus()),
                'author_id' => $article->getAuthorId()->getValue(),
                'created_at' => $article->getCreatedAt(),
            ]);

            $this->syncCategories($record, $article->getCategories());

            return $record->toDomain($article->getEvents());
        });
    }

    private function syncCategories(ArticleRecord $record, ArticleCategoryCollection $articleCategoryCollection): void
    {
        $record->categories()->sync($articleCategoryCollection->getIdsArray());
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
            'author_id' => $article->getAuthorId()->getValue(),
            'created_at' => $article->getCreatedAt(),
        ]);

        $this->syncCategories($record, $article->getCategories());

        return $record->toDomain($article->getEvents());
    }

    public function paginate(int $currentPage = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator
    {
        $paginator = ArticleRecord::query()->search($criteria)->paginate($perPage, ['*'], 'current_page', $currentPage);

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
