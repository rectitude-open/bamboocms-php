<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;

it('can persist draft article data correctly', function () {
    $article = Article::createDraft(new ArticleId(0), 'My Article', 'This is my article body', new CarbonImmutable());
    $articleRepository = new ArticleRepository();

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::draft()),
    ]);
});

it('can persist published article data correctly', function () {
    $article = Article::createPublished(new ArticleId(0), 'My Article', 'This is my article body', new CarbonImmutable());
    $articleRepository = new ArticleRepository();

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::published()),
    ]);
});
