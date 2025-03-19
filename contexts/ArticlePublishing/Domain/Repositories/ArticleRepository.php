<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Domain\Repositories;

use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepository
{
    public function create(Article $article): Article;

    public function getById(ArticleId $articleId): Article;

    public function update(Article $article): Article;

    public function paginate(int $currentPage = 1, int $perPage = 10, array $criteria = []): LengthAwarePaginator;

    public function delete(Article $article): void;
}
