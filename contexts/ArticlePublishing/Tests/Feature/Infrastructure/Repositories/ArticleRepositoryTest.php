<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;

it('can persist article data correctly', function () {
    $article = new Article(new ArticleId(0), 'My Article', 'This is my article content', new CarbonImmutable());
    $articleRepository = new ArticleRepository();

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'content' => 'This is my article content',
    ]);
});
